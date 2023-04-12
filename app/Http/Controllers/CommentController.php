<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Services\CommentService;
use Illuminate\Http\Request;

use App\Traits\CommentPaginate;

use Exception;

class CommentController extends Controller
{
  use CommentPaginate;

  protected CommentService $commentService;

  public function __construct(CommentService $commentService)
  {
    $this->commentService = $commentService;
  }

  public function store(Request $request, string $tableName)
  {
    $data = $request->all();

    try {
      $result = $this->commentService->store(request: $request, tableName: $tableName, data: $data);
      $error = gettype($result) === 'object' ? json_decode($result->content(), true)['error'] : [];

      if (!empty($error)) {
        throw new Exception($error);
      }

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->route('board.show', ['idx' => $result['board_idx'], 'tableName' => $tableName]);
  }

  public function edit(int $idx, string $tableName)
  {
    try {
      $result = $this->commentService->edit(idx: $idx);
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }

  public function update(int $idx, Request $request, string $tableName)
  {
    try {
      $result = $this->commentService->update(idx: $idx, request: $request);
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }

  public function destroy(int $idx, string $tableName)
  {
    try {
      $result = $this->commentService->destroy(idx: $idx);
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }

  // 페이징 리스트 - commentGetList
  public function list(string $tableName, int $idx, int $offset)
  {
    $commentData = $this->commentGetList(tableName: 'comment_' . $tableName, boardIdx: $idx, offset: $offset);

    return $commentData['data'];
  }
}
