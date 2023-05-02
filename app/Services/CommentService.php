<?php

namespace App\Services;

use App\Repositories\BoardRepository;
use App\Repositories\CommentRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CommentService
{
    protected CommentRepository $commentRepository;
    protected BoardRepository $boardRepository;

    public function __construct(CommentRepository $commentRepository, BoardRepository $boardRepository, Request $request)
    {
        $this->commentRepository = $commentRepository;
        $this->boardRepository = $boardRepository;

        // 동적 테이블명을 위한 로직 - 서비스 컨테이너에서 클래스 인스턴스 의존성 해결 및 변수 전달을 위해 table name 재할당
        $dynamicParameters = optional($request->route())->parameters()['tableName'] ?? null;
        $dynamicTableName = empty($dynamicParameters) ? 'basic' : $dynamicParameters;
        $this->commentRepository = app($this->commentRepository::class, ['tableName' => 'comment_' . $dynamicTableName]);
        $this->boardRepository = app($this->boardRepository::class, ['tableName' => 'board_' . $dynamicTableName]);
    }

    public function store(Request $request, string $tableName, array $data = [])
    {
        $validator = Validator::make($data, [
            'board_url' => 'required',
            'board_idx' => 'required',
            'comment_content' => 'required',
            'comment_idx' => 'int',
            'group_idx' => 'int',
            'group_order' => 'int',
        ])->validate();

        $referer = $request->headers->get('referer');
        $route = route('board.show', ['idx' => $validator['board_idx'], 'tableName' => $tableName]);
        $user = Auth::user()['email'];

        // 전체 주소 및 board idx로 이전 referer와 비교하여 잘못된 경로 접근형식일 시 에러 반환
        if ($validator['board_url'] !== $referer || $referer !== $route) {
            return response()->json(['error' => '잘못된 접근 경로 입니다.']);
        }

        $comment = $this->commentRepository->create(data: [
            'board_idx' => $validator['board_idx'],
            'comment_writer' => $user,
            'comment_content' => $validator['comment_content'],
        ]);

        // 내 코멘트 등록따른 업데이트 처리
        $myComment = $this->commentRepository->where(boardIdx: $comment['board_idx']);

        // 코멘트 총 갯수 업데이트
        $this->boardRepository->findUpdate(idx: $comment['board_idx'], data: [
            'all_comment' => $myComment->count()
        ]);

        // 최근 게시글
        $myCommentArr = $this->commentRepository->dynamicRecentList(query: $myComment);

        // 댓글만 달때
        if (empty($validator['comment_idx']) && empty($myCommentArr['parent_idx'])) {
            $this->commentRepository->dynamicUpdate(query: $myComment, idx: $myCommentArr['idx'], data: [
                'depth_idx' => $myCommentArr['idx'],
                'group_idx' => $myCommentArr['idx']
            ]);
        }

        // 대댓글 달때
        if (!empty($validator['comment_idx'])) {
            $parentArr = $this->commentRepository->dynamicMyList(query: $myComment, commentIdx: $validator['comment_idx']);

            $this->commentRepository->dynamicUpdate(query: $myCommentArr, idx: $myCommentArr['idx'], data: [
                'depth_idx' => $parentArr['depth_idx'] . '-' . $myCommentArr['idx'],
                'group_idx' => $validator['group_idx'],
                'group_order' => ($validator['group_order'] + 1)
            ]);
        }

        return $validator;
    }

    public function edit(int $idx)
    {
        $comment = $this->commentRepository->findList($idx);
        $user = Auth::user()['email'];

        if ($comment['comment_writer'] !== $user) {
            return response()->json(['error' => '잘못된 접근 경로 입니다.']);
        }

        return response()->json([
            'comment_content' => $comment['comment_content']
        ]);
    }

    public function update(int $idx, Request $request)
    {
        $user = Auth::user()['email'];
        $comment = $this->commentRepository->findList($idx);
        $commentContent = $request->input()['comment_content'];

        if ($comment['comment_writer'] !== $user) {
            return response()->json(['error' => '잘못된 접근 경로 입니다.']);
        }

        $this->commentRepository->update(whereData: ['idx' => $comment['idx']], data: ['comment_content' => $commentContent]);

        return response()->json([
            'comment_content' => $commentContent
        ]);
    }

    public function destroy(int $idx)
    {
        //초깃값
        $userEmail = Auth::user()['email'];
        $comment = $this->commentRepository->findList($idx);

        if ($comment['comment_writer'] !== $userEmail) {
            return response()->json([
                'error' => '비정상적인 접근입니다.'
            ]);
        }

        if ($comment['comment_state'] === 'y') {
            return response()->json([
                'error' => '이미 삭제된 덧글입니다.'
            ]);
        }

        DB::beginTransaction();
        try {
            $this->commentRepository->update(whereData: ['idx' => $idx, 'comment_writer' => $userEmail], data: [
                'comment_state' => 'y',
                'comment_deleted_at' => date('Y-m-d H:i:s')
            ]);

            DB::commit();

            return response()->json([
                'message' => '삭제를 성공하셨습니다.',
                'comment_deleted_at' => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            DB::rollback();

            return response()->json(['error' => $e->getMessage()]);
        }
    }
}
