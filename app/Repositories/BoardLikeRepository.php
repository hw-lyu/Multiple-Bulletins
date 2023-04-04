<?php

namespace App\Repositories;

use App\Interfaces\BoardLikeRepositoryInterface;

use App\Models\BoardLike;

class BoardLikeRepository implements BoardLikeRepositoryInterface
{
  protected BoardLike $boardLike;

  public function __construct(BoardLike $boardLike)
  {
    return $this->boardLike = $boardLike;
  }

  public function create(array $data = [])
  {
    return $this->boardLike->create($data);
  }
}
