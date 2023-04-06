<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\BoardService;

use Illuminate\Http\Request;
use App\Traits\CommentPaginate;

use Exception;

class BoardController extends Controller
{
  use CommentPaginate;

  protected BoardService $boardService;

  public function __construct(BoardService $boardService)
  {
    $this->middleware('auth')->only('store', 'create');
    $this->boardService = $boardService;
  }

  public function create()
  {
    return view('board.writing');
  }

  public function store(Request $request)
  {
    $data = $request->all();

    try {
      $result = $this->boardService->storePost(request: $request, data: $data);

      if (empty($result['board'])) {
        throw new Exception('게시물을 다시 작성해주세요.');
      }

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->route('boards.show', ['board' => $result['board']]);
  }

  public function show(int $idx, Request $request)
  {
    try {
      $result = $this->boardService->showPost(request: $request, idx: $idx);

      if (!empty($result['error'])) {
        throw new Exception($result['error']);
      }
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return view('board.detail', $result);
  }

  public function edit(int $idx)
  {
    try {
      $result = $this->boardService->editPost($idx);

      if (!empty($result['error'])) {
        throw new Exception($result['error']);
      }
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return view('board.modify', ['idx' => $idx, 'boardDetail' => $result['boardDetail']]);
  }

  public function update(int $idx, Request $request)
  {
    $data = $request->all();

    try {
      $result = $this->boardService->updatePost(idx: $idx, request: $request, data: $data);

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->route('boards.show', ['board' => $result['board']]);
  }

  public function destroy(int $idx)
  {
    try {
      $result = $this->boardService->destroyPost($idx);

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }

  public function like(int $idx)
  {
    try {
      $result = $this->boardService->likePost(idx: $idx);

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }
}
