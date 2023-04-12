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

  public function getList()
  {
    return $this->boardTableList->get();
  }
}
