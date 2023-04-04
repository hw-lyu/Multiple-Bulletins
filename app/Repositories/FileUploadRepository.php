<?php

namespace App\Repositories;

use App\Interfaces\FileUploadRepositoryInterface;
use App\Models\BoardFiles;

class FileUploadRepository implements FileUploadRepositoryInterface
{
  protected BoardFiles $boardFiles;

  public function __construct(BoardFiles $boardFiles)
  {
    $this->boardFiles = $boardFiles;
  }

  public function create(array $data = [])
  {
    return $this->boardFiles->create($data);
  }

  public function delete(string $userEmail, string $fileURL)
  {
    return $this->boardFiles
      ->where('user_email', $userEmail)
      ->where('file_url', $fileURL)
      ->delete();
  }

}
