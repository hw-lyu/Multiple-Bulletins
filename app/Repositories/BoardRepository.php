<?php

namespace App\Repositories;

use App\Interfaces\BoardRepositoryInterface;
use App\Models\Board;

class BoardRepository implements BoardRepositoryInterface
{
  protected Board $board;

  public function __construct(Board $board)
  {
    $this->board = $board;
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

}
