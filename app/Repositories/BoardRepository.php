<?php

namespace App\Repositories;

use App\Interfaces\BoardRepositoryInterface;
use App\Models\Board;

class BoardRepository implements BoardRepositoryInterface
{
  protected Board $board;

  public function __construct(Board $board, string $tableName = 'board_basic')
  {
    $this->board = $board;
    $this->board->setTable($tableName);
  }

  public function create(array $data = [])
  {
    return $this->board->create($data);
  }

  public function update(int $idx, string $userEmail, array $data = [])
  {
    return $this->board->where('idx', $idx)
      ->where('user_email', $userEmail)
      ->update($data);
  }

  public function findUpdate(int $idx, array $data = [])
  {
    return $this->board->find($idx)
      ->update($data);
  }

  public function getByIdx(int $idx)
  {
    return $this->board->find($idx);
  }

  public function incrementBoardViews(int $idx, object $query, string $key = 'views')
  {
    return $this->board->withoutTimestamps(fn() => ($query)->increment($key, 1));
  }

  public function getList(string $boardState = 'n', int $paginateNum = 3)
  {
    return $this->board->where('board_state', $boardState)
      ->orderBy('idx', 'desc')
      ->paginate($paginateNum);
  }

  public function getAllList(int $paginateNum = 3)
  {
    return $this->board->orderBy('idx', 'desc')
      ->paginate($paginateNum);
  }

}
