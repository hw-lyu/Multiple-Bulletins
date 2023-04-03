<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UploadController extends Controller
{
  protected FileUploadService $fileUploadService;

  public function __construct(FileUploadService $fileUploadService)
  {
    $this->middleware('auth')->only('store');
    $this->fileUploadService = $fileUploadService;
  }

  public function store(Request $request)
  {
    $data = $request->all();
    $createData = [
      'user_email' => Auth::user()['email']
    ];

    return $this->fileUploadService->store(request: $request, data: $data, createData: $createData);
  }

}
