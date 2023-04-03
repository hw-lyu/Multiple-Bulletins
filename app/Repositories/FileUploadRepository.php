<?php

namespace App\Repositories;

use App\Interfaces\FileUploadRepositoryInterface;
use App\Models\BoardFiles;

class FileUploadRepository implements FileUploadRepositoryInterface
{
  protected BoardFiles $boardFiles;

  public function __construct(BoardFiles $boardFiles) {
    $this->boardFiles = $boardFiles;
  }

  public function create(array $data = [])
  {
    return $this->boardFiles->create($data);
  }
}
