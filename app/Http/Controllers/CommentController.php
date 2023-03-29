<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\DB;
use App\Traits\CommentPaginate;

use App\Models\Board;
use App\Models\Comment;

class CommentController extends Controller
{
  use CommentPaginate;

  public function store(Request $request)
  {
    $validated = $request->validate([
      'board_url' => 'required',
      'board_idx' => 'required',
      'comment_content' => 'required',
      'comment_idx' => 'int',
      'group_idx' => 'int',
      'group_order' => 'int'
    ]);

    $referer = $request->headers->get('referer');

    if ($validated['board_url'] !== $referer) {
      return redirect()->back()->withErrors(['error' => '잘못된 접근 경로 입니다.']);
    }

    $user = Auth::user()['email'];

    $comment = Comment::create([
      'board_idx' => $validated['board_idx'],
      'comment_writer' => $user,
      'comment_content' => $validated['comment_content'],
    ]);

    // 내 코멘트 등록따른 업데이트 처리
    $myComment = Comment::where('board_idx', $comment['board_idx']);

    // 코멘트 총 갯수 업데이트
    Board::find($comment['board_idx'])
      ->update([
        'all_comment' => $myComment->count()
      ]);

    // 코멘트에 parent_idx가 없으면 추가
    $myCommentArr = $myComment->orderBy('idx', 'desc')->first();

    // 댓글만 달때
    if (empty($validated['comment_idx']) && empty($myCommentArr['parent_idx'])) {
      $myComment->where('idx', $myCommentArr['idx'])
        ->update([
          'depth_idx' => $myCommentArr['idx'],
          'group_idx' => $myCommentArr['idx']
        ]);
    }

    // 대댓글 달때
    if (!empty($validated['comment_idx'])) {
      $parentArr = $myComment->where('idx', $validated['comment_idx'])->first();

      $myCommentArr->where('idx', $myCommentArr['idx'])
        ->update([
          'depth_idx' => $parentArr['depth_idx'] . '-' . $myCommentArr['idx'],
          'group_idx' => $validated['group_idx'],
          'group_order' => ($validated['group_order'] + 1)
        ]);
    }

    return redirect()->route('boards.show', ['board' => $validated['board_idx']]);
  }

  public function edit(int $idx)
  {
    $comment = Comment::find($idx);
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
    $comment = Comment::find($idx);
    $commentContent = $request->input()['comment_content'];

    if ($comment['comment_writer'] !== $user) {
      return response()->json(['error' => '잘못된 접근 경로 입니다.']);
    }

    Comment::where('idx', $comment['idx'])
      ->update(['comment_content' => $commentContent]);

    return response()->json([
      'comment_content' => $commentContent
    ]);
  }

  public function destroy(int $idx)
  {
    //초깃값
    $userEmail = Auth::user()['email'];
    $commentData = Comment::find($idx);

    if ($commentData['comment_writer'] !== $userEmail) {
      return response()->json([
        'error' => '비정상적인 접근입니다.'
      ]);
    }

    if ($commentData['comment_state'] === 'y') {
      return response()->json([
        'error' => '이미 삭제된 덧글입니다.'
      ]);
    }

    DB::beginTransaction();
    try {
      Comment::where('idx', $idx)
        ->where('comment_writer', $userEmail)
        ->update([
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

  // 별도의 함수
  public function list(int $idx, int $offset)
  {
    //페이징
    $commentData = $this->commentGetList($idx, $offset);

    return $commentData['data'];
  }
}
