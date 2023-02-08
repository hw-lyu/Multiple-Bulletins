<?php

namespace app\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\Comment;
use Illuminate\Support\Facades\DB;
use App\Traits\CommentPaginate;

class CommentController extends Controller
{
  use CommentPaginate;

  public function store(Request $request)
  {
    $validated = $request->validate([
      'board_url' => 'required',
      'board_idx' => 'required',
      'comment_content' => 'required',
      'parent_idx' => 'int'
    ]);
    $referer = $request->headers->get('referer');

    if ($validated['board_url'] !== $referer) {
      return redirect()->back()->withErrors(['error' => '잘못된 접근 경로 입니다.']);
    }

    $user = Auth::user()['email'];

    // 코멘트 등록시 자기 코멘트에는 덧글 못달게 하기
    if (!empty($validated['parent_idx'])) {
      $parentDate = Comment::where('idx', $validated['parent_idx'])->first();
      if ($user === $parentDate['comment_writer']) return redirect()->back()->withErrors(['error' => '내 댓글에는 댓글을 달 수 없습니다.']);
    }

    $comment = Comment::create([
      'board_idx' => $validated['board_idx'],
      'comment_writer' => $user,
      'comment_content' => $validated['comment_content'],
      'parent_idx' => $validated['parent_idx'] ?? null
    ]);

    // 부모 idx가 없는 경우 자기 idx 업데이트
    if ($comment['parent_idx'] === null) {
      Comment::where('idx', $comment['idx'])
        ->update(['parent_idx' => $comment['idx']]);
    }

    return redirect()->route('boards.show', ['board' => $validated['board_idx']]);
  }

  public function edit(int $idx)
  {
    $comment = Comment::find($idx);
    $user = Auth::user()['email'];

    if ($comment['comment_writer'] !== $user) {
      return redirect()->back()->withErrors(['error' => '잘못된 접근 경로 입니다.']);
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

    //에러코드 다시 생성해서 보내줘야할듯...!!
    if ($comment['comment_writer'] !== $user) {
      return redirect()->back()->withErrors(['error' => '잘못된 접근 경로 입니다.']);
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
        'message' => '비정상적인 접근입니다.'
      ]);
    }

    if ($commentData['comment_state'] === 'y') {
      return response()->json([
        'message' => '이미 삭제된 덧글입니다.'
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
