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

  public function index(string $tableName = 'basic')
  {
    try {
      $result = $this->boardService->getList(tableName: $tableName);
      if (!empty($result['error'])) {
        throw new Exception($result['error']);
      }
    } catch (Exception $e) {
      return redirect()->route('home')->withErrors(['error' => $e->getMessage()]);
    }

    return view('index', $result);
  }

  public function create(string $tableName)
  {
    return view('board.writing', ['tableName' => $tableName]);
  }

  public function store(string $tableName, Request $request)
  {
    $data = $request->all();
    try {
      $result = $this->boardService->storePost(tableName: $tableName, request: $request, data: $data);

      if (empty($result['boardIdx'])) {
        throw new Exception('게시물을 다시 작성해주세요.');
      }

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->route('board.show', ['idx' => $result['boardIdx'], 'tableName' => $tableName]);
  }

  public function show(string $tableName, int $idx, Request $request)
  {
    try {
      $result = $this->boardService->showPost(request: $request, tableName: $tableName, idx: $idx);
      $boardTitle = $this->boardService->getBoardTitle(tableName: $tableName);

      if (!empty($result['error'])) {
        throw new Exception($result['error']);
      }
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return view('board.detail', ['tableName' => $tableName, 'boardTitle' => $boardTitle, ...$result]);
  }

  public function edit(string $tableName, int $idx)
  {
    try {
      $result = $this->boardService->editPost($idx);

      if (!empty($result['error'])) {
        throw new Exception($result['error']);
      }
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return view('board.modify', ['idx' => $idx, 'boardDetail' => $result['boardDetail'], 'tableName' => $tableName]);
  }

  public function update(string $tableName, int $idx, Request $request)
  {
    $data = $request->all();

    try {
      $result = $this->boardService->updatePost(tableName: $tableName, idx: $idx, request: $request, data: $data);

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->route('board.show', ['idx' => $result['boardIdx'], 'tableName' => $tableName]);
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

  public function like(string $tableName, int $idx)
  {
    try {
      $result = $this->boardService->likePost(tableName: $tableName, idx: $idx);

    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }
}
