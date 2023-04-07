<?php

namespace App\Interfaces;

interface CommentRepositoryInterface
{
  public function create(array $data = []);
}
