<?php

namespace App\Interfaces;

interface BoardRepositoryInterface
{
  public function create(array $data = []);

  public function update(int $idx, string $userEmail, array $data = []);

  public function getByIdx(int $idx);

  public function incrementBoardViews(int $idx, object $query, string $key = 'views');
}
