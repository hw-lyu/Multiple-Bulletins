<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use Exception;

class UploadController extends Controller
{
  protected FileUploadService $fileUploadService;

  public function __construct(FileUploadService $fileUploadService)
  {
    $this->middleware('auth')->only('store');
    $this->fileUploadService = $fileUploadService;
  }

  public function store(Request $request, string $tableName)
  {
    $data = $request->all();
    $createData = [
      'user_email' => Auth::user()['email'],
      'table_name' => $tableName
    ];

    try {
      $result = $this->fileUploadService->store(request: $request, data: $data, createData: $createData);

      if (gettype($result) === 'array' && !empty($result['error'])) {
        throw new Exception($result['error']);
      }

    } catch (Exception $e) {
      return response()->json(['error' => $e->getMessage()]);
    }

    return $result;
  }
}
