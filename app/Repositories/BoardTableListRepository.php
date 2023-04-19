<?php

namespace App\Repositories;

use App\Models\BoardTableList;

class BoardTableListRepository
{
  protected BoardTableList $boardTableList;

  public function __construct(BoardTableList $boardTableList)
  {
    $this->boardTableList = $boardTableList;
  }

  public function getList(array $whereData = ['col' => 'val1'])
  {
    return $this->boardTableList
      ->where($whereData)
      ->orderBy('idx', 'desc')
      ->get();
  }

  public function getAllList()
  {
    return $this->boardTableList
      ->orderBy('idx', 'desc')
      ->get();
  }

  public function getBoardTitle(string $tableName)
  {
    return $this->boardTableList
      ->where('table_name', $tableName)
      ->value('table_board_title');
  }
}
