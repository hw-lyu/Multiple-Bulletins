<?php

namespace App\Interfaces;

interface FileUploadRepositoryInterface
{
  public function create(array $data = []);

  public function delete(string $userEmail, string $fileURL);
}
