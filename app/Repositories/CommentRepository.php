<?php

namespace App\Repositories;

use App\Interfaces\CommentRepositoryInterface;
use App\Models\Comment;

class CommentRepository implements CommentRepositoryInterface
{
  protected Comment $comment;

  public function __construct(Comment $comment)
  {
    $this->comment = $comment;
  }

  public function create(array $data = [])
  {
    return $this->comment->create($data);
  }

  public function findList(int $idx)
  {
    return $this->comment->find($idx);
  }

  public function update(array $whereData = ['col1' => 'col1'], array $data = [])
  {
    return $this->comment->where($whereData)
      ->update($data);
  }

  public function where(int $boardIdx)
  {
    return $this->comment->where('board_idx', $boardIdx);
  }

  public function dynamicRecentList(object $query)
  {
    return $query->orderBy('idx', 'desc')->first();
  }

  public function dynamicMyList(object $query, int $commentIdx)
  {
    return $query->where('idx', $commentIdx)->first();
  }

  public function dynamicUpdate(object $query, int $idx, array $data = [])
  {
    return $query->where('idx', $idx)
      ->update($data);
  }
}
