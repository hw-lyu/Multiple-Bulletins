<?php

namespace App\Services;

use App\Traits\CommentPaginate;

use App\Repositories\BoardRepository;
use App\Repositories\BoardLikeRepository;
use App\Repositories\FileUploadRepository;
use App\Repositories\BoardTableListRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

use Exception;

class BoardService
{
  use CommentPaginate;

  protected BoardRepository $boardRepository;
  protected BoardLikeRepository $boardLikeRepository;
  protected FileUploadRepository $fileUploadRepository;
  protected BoardTableListRepository $boardTableListRepository;
  protected object $setBoard;

  public function __construct(BoardRepository $boardRepository, BoardLikeRepository $boardLikeRepository, FileUploadRepository $fileUploadRepository, BoardTableListRepository $boardTableListRepository, Request $request)
  {
    $this->boardRepository = $boardRepository;
    $this->boardLikeRepository = $boardLikeRepository;
    $this->fileUploadRepository = $fileUploadRepository;
    $this->boardTableListRepository = $boardTableListRepository;

    // 동적 테이블명을 위한 로직 - 서비스 컨테이너에서 클래스 인스턴스 의존성 해결 및 변수 전달을 위해 table name 재할당
    $dynamicParameters = optional($request->route())->parameters()['tableName'] ?? null;
    $dynamicTableName = empty($dynamicParameters) ? 'board_basic' : 'board_' . $dynamicParameters;
    $this->boardRepository = app($this->boardRepository::class, ['tableName' => $dynamicTableName]);
  }

  public function storePost(Request $request, array $data = [])
  {
    $validator = Validator::make($data, [
      'board_title' => 'required|max:255',
      'board_cate' => 'required|in:카테1,카테2,카테3',
      'photo_state' => 'required',
      'board_content' => 'required',
      'board_content_delete_img' => 'array'
    ])->validate();

    // 초깃값
    $userEmail = Auth::user()['email'];
    $boardContentDeleteImg = $validator['board_content_delete_img'] ?? [];

    DB::beginTransaction();
    try {
      // 해당파일 삭제 및 디비 값 삭제
      // * n+1 해결 해야함. 54-61
      if (!empty($boardContentDeleteImg)) {
        foreach (array_unique($boardContentDeleteImg) as $fileImg) {
          $str = str_replace(url('') . '/storage/img/', '', $fileImg);

          Storage::delete('storage/img/' . $str);
          Storage::disk('local')->delete('public/img/' . $str);

          $this->fileUploadRepository->delete(userEmail: $userEmail, fileURL: $fileImg);
        }
      }

      $board = $this->boardRepository->create(data: [
        'user_email' => $userEmail,
        'board_title' => $validator['board_title'],
        'board_cate' => $validator['board_cate'],
        'photo_state' => $validator['photo_state'],
        'board_content' => $validator['board_content']
      ]);

      DB::commit();

      return ['status' => 200, 'boardIdx' => $board['idx']];

    } catch (Exception $e) {
      DB::rollback();

      //임시 세션 저장후 임시값 활용하여 폼 양식 저장
      $request->flash();

      return ['error' => $e->getMessage()];
    }
  }

  public function showPost(Request $request, string $tableName, int $idx)
  {
    $boardDetail = $this->boardRepository->getByIdx($idx);
    $auth = Auth::user() ?? [];
    $grade = !empty($auth['grade']) ? $auth['grade'] : 0;

    // 페이징
    $commentData = $this->commentGetList(tableName: 'comment_' . $tableName, boardIdx: $idx);

    if ($boardDetail === null) {
      return ['error' => '해당 글이 없습니다.'];
    }

    //게시판 볼 때 마다 조회수 증가 --- `updated_at` is not changed | 조회수 중복체크 미정
    $this->boardRepository->incrementBoardViews(idx: $idx, query: $boardDetail);

    $boardUrl = $request->fullUrl();
    $commentView = $request->query('comment_view');
    $boardDetailAuth = (Auth::user()['email'] ?? null) === $boardDetail['user_email'] ? 1 : 0;
    $boardUpdatedDateState = !!abs(strtotime($boardDetail['view_created_at']) - strtotime($boardDetail['view_updated_at']));

    return ['idx' => $idx, 'boardDetail' => $boardDetail, 'commentData' => $commentData, 'boardDetailAuth' => $boardDetailAuth, 'boardUpdatedDateState' => $boardUpdatedDateState, 'boardUrl' => $boardUrl, 'grade' => $grade, 'commentView' => $commentView];
  }

  public function editPost(int $idx)
  {
    $boardDetail = $this->boardRepository->getByIdx($idx);
    $boardDetailAuth = (Auth::user()['email'] ?? null) === ($boardDetail['user_email'] ?? null) ? 1 : 0;

    if ($boardDetailAuth === 0) {
      return ['error' => '작성자 글에 접속할 수 없습니다.'];
    }

    if ($boardDetail === null) {
      return ['error' => '해당 글이 없습니다.'];
    }

    return ['idx' => $idx, 'boardDetail' => $boardDetail];
  }

  public function updatePost(int $idx, Request $request, array $data = [])
  {
    $validator = Validator::make($data, [
      'board_title' => 'required|max:255',
      'board_cate' => 'required',
      'photo_state' => 'required',
      'board_content' => 'required',
      'board_content_delete_img' => 'array'
    ])->validate();

    // 초깃값
    $userEmail = Auth::user()['email'];
    $boardContentDeleteImg = $validated['board_content_delete_img'] ?? [];

    DB::beginTransaction();
    try {
      // 해당파일 삭제 및 디비 값 삭제
      if (!empty($boardContentDeleteImg)) {
        foreach (array_unique($boardContentDeleteImg) as $fileImg) {
          $str = str_replace(url('') . '/storage/img/', '', $fileImg);

          Storage::delete('storage/img/' . $str);
          Storage::disk('local')->delete('public/img/' . $str);

          $this->fileUploadRepository->delete(userEmail: $userEmail, fileURL: $fileImg);
        }
      }

      $updateArr = [
        'board_title' => $validator['board_title'],
        'board_cate' => $validator['board_cate'],
        'photo_state' => $validator['photo_state'],
        'board_content' => $validator['board_content']
      ];

      $this->boardRepository->update(idx: $idx, userEmail: $userEmail, data: $updateArr);

      DB::commit();

      return ['boardIdx' => $idx];

    } catch (Exception $e) {
      DB::rollback();

      //임시 세션 저장후 임시값 활용하여 폼 양식 저장
      $request->flash();

      return ['error' => $e->getMessage()];
    }
  }

  public function destroyPost(int $idx)
  {
    //초깃값
    $userEmail = Auth::user()['email'];

    DB::beginTransaction();
    try {
      $updateArr = [
        'board_state' => 'y',
        'deleted_at' => now()
      ];

      $this->boardRepository->update(idx: $idx, userEmail: $userEmail, data: $updateArr);

      DB::commit();

      return ['remove' => $updateArr];

    } catch (Exception $e) {
      DB::rollback();

      return ['error' => $e->getMessage()];
    }
  }

  public function likePost(int $idx)
  {
    //초깃값
    header('Content-Type', 'application/json');
    $userEmail = Auth::user()['email'];
    $board = $this->boardRepository->getByIdx($idx);

    if ($board['user_email'] === $userEmail) {
      return response()->json(['error' => '자기가 쓴 글은 게시글은 좋아요를 클릭할 수 없습니다.'], 409);
    }

    DB::beginTransaction();
    try {
      $this->boardLikeRepository->create(data: [
        'user_email' => $userEmail,
        'board_idx' => $idx,
      ]);

      //좋아요 추가
      $this->boardRepository->incrementBoardViews(idx: $idx, query: $board, key: 'view_like');

      DB::commit();

      return [
        'board_idx' => $board['idx'],
        'view_like' => $board['view_like']
      ];

    } catch (Exception $e) {
      DB::rollback();

      if ($e->errorInfo[1] === 1062) {
        return response()->json(['error' => '해당글의 투표는 한번만 가능합니다.'], 409);
      }

      return response()->json(['error' => $e->getMessage()], 500);
    }
  }

  public function getList(string $tableName)
  {
    $auth = Auth::user() ?? [];
    $boardTableListData = $this->boardTableListRepository->getList();

    // $auth['grade'] - 1: 일반회원, 2: 관리자
    if (!empty($auth['grade']) && $auth['grade'] === 2) {
      $listData = $this->boardRepository->getAllList(paginateNum: '3');
    } else {
      $listData = $this->boardRepository->getList(boardState: 'N', paginateNum: '3');
    }

    return ['auth' => $auth, 'listData' => $listData, 'boardTableListData' => $boardTableListData, 'tableName' => $tableName];
  }
}
