<?php

namespace App\Interfaces;

interface FileUploadRepositoryInterface
{
  public function create(array $data = []);

  public function delete(array $whereData = ['col' => 'val1']);
}
