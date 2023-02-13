<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

use App\Models\Board;
use App\Models\BoardFiles;
use App\Models\BoardLike;
use App\Traits\CommentPaginate;

class BoardController extends Controller
{
  use CommentPaginate;

  public function __construct()
  {
    $this->middleware('auth')->only('store', 'create');
  }

  public function create()
  {
    return view('board.writing');
  }

  public function store(Request $request)
  {
    $validated = $request->validate([
      'board_title' => 'required|max:255',
      'board_cate' => 'required|in:카테1,카테2,카테3',
      'photo_state' => 'required',
      'board_content' => 'required',
      'board_content_delete_img' => 'array'
    ]);

    //초깃값
    $userEmail = Auth::user()['email'];
    $boardContentDeleteImg = $validated['board_content_delete_img'] ?? [];

    DB::beginTransaction();
    try {
      //해당파일 삭제 및 디비 값 삭제
      if (!empty($boardContentDeleteImg)) {
        foreach (array_unique($boardContentDeleteImg) as $img) {
          $str = str_replace(url('') . '/storage/img/', '', $img);

          Storage::delete('storage/img/' . $str);
          Storage::disk('local')->delete('public/img/' . $str);

          BoardFiles::where('user_email', $userEmail)
            ->where('file_url', $img)
            ->delete();
        }
      }

      $board = Board::create([
        'user_email' => $userEmail,
        'board_title' => $validated['board_title'],
        'board_cate' => $validated['board_cate'],
        'photo_state' => $validated['photo_state'],
        'board_content' => $validated['board_content']
      ]);

      DB::commit();

      return redirect()->route('boards.show', ['board' => $board['idx']]);

    } catch (\Exception $e) {
      DB::rollback();

      //임시 세션 저장후 임시값 활용하여 폼 양식 저장
      $request->flash();

      return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
  }

  public function show(int $idx, Request $request)
  {
    $boardDetail = Board::find($idx);
    $auth = Auth::user() ?? [];
    $grade = !empty($auth['grade']) ? $auth['grade'] : 0;

    //페이징
    $commentData = $this->commentGetList($idx);

    if ($boardDetail === null) {
      return redirect()->route('home');
    }

    //게시판 볼 때 마다 조회수 증가 --- 조회수 중복체크 미정
    Board::withoutTimestamps(fn() => $boardDetail->increment('views', 1));

    $boardUrl = $request->url();
    $boardDetailAuth = (Auth::user()['email'] ?? null) === $boardDetail['user_email'] ? 1 : 0;
    $boardUpdatedDateState = !!abs(strtotime($boardDetail['view_created_at']) - strtotime($boardDetail['view_updated_at']));

    return view('board.detail', ['idx' => $idx, 'boardDetail' => $boardDetail, 'commentData' => $commentData, 'boardDetailAuth' => $boardDetailAuth, 'boardUpdatedDateState' => $boardUpdatedDateState, 'boardUrl' => $boardUrl, 'grade' => $grade]);
  }

  public function edit(int $idx)
  {
    $boardDetail = Board::where('idx', $idx)
      ->first();
    $boardDetailAuth = (Auth::user()['email'] ?? null) === $boardDetail['user_email'] ? 1 : 0;

    if ($boardDetailAuth === 0) {
      echo "<script>alert('글에 접속할 수 없습니다.');</script>";
      return view('index');
    }

    if ($boardDetail === null) {
      echo "<script>alert('글을 찾을 수 없습니다.');</script>";
      return view('index');
    }

    return view('board.modify', ['idx' => $idx, 'boardDetail' => $boardDetail]);
  }

  public function update(int $idx, Request $request)
  {
    $validated = $request->validate([
      'board_title' => 'required|max:255',
      'board_cate' => 'required',
      'photo_state' => 'required',
      'board_content' => 'required',
      'board_content_delete_img' => 'array'
    ]);

    //초깃값
    $userEmail = Auth::user()['email'];
    $boardContentDeleteImg = $validated['board_content_delete_img'] ?? [];

    DB::beginTransaction();
    try {
      //해당파일 삭제 및 디비 값 삭제
      if (!empty($boardContentDeleteImg)) {
        foreach (array_unique($boardContentDeleteImg) as $img) {
          $str = str_replace(url('') . '/storage/img/', '', $img);

          Storage::delete('storage/img/' . $str);
          Storage::disk('local')->delete('public/img/' . $str);

          BoardFiles::where('user_email', $userEmail)
            ->where('file_url', $img)
            ->delete();
        }
      }

      $board = Board::where('idx', $idx)
        ->where('user_email', $userEmail)
        ->update([
          'board_title' => $validated['board_title'],
          'board_cate' => $validated['board_cate'],
          'photo_state' => $validated['photo_state'],
          'board_content' => $validated['board_content']
        ]);

      DB::commit();

      return redirect()->route('boards.show', ['board' => $idx]);

    } catch (\Exception $e) {
      DB::rollback();

      //임시 세션 저장후 임시값 활용하여 폼 양식 저장
      $request->flash();

      return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
  }

  public function destroy(int $idx)
  {
    //초깃값
    $userEmail = Auth::user()['email'];

    DB::beginTransaction();
    try {
      Board::where('idx', $idx)
        ->where('user_email', $userEmail)
        ->update([
          'board_state' => 'y',
          'deleted_at' => now()
        ]);

      DB::commit();

      return "remove";

    } catch (\Exception $e) {
      DB::rollback();

      return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
  }

  public function like(int $idx)
  {
    //초깃값
    $userEmail = Auth::user()['email'];

    //질의요청
    $board = Board::find($idx);
    if ($board['user_email'] === $userEmail) {
      return response()->json(['error' => '자기가 쓴 글은 게시글은 좋아요를 클릭할 수 없습니다.'], 409);
    }

    DB::beginTransaction();
    try {
      $boardLike = BoardLike::create([
        'user_email' => $userEmail,
        'board_idx' => $idx,
      ]);

      //좋아요 추가
      Board::withoutTimestamps(fn() => $board->increment('view_like', 1));

      DB::commit();

      return response()->json([
        'board_idx' => $board['idx'],
        'view_like' => $board['view_like']
      ]);

    } catch (\Exception $e) {
      DB::rollback();

      if ($e->errorInfo[1] === 1062) {
        return response()->json(['error' => '해당글의 투표는 한번만 가능합니다.'], 409);
      }

      return response()->json(['error' => $e->getMessage()], 500);
    }
  }
}
