<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\BoardFiles;
use Illuminate\Support\Facades\Storage;

class UploadController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth')->only('store');
  }

  public function store(Request $request) : \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
  {
    DB::beginTransaction();
    try {

      if ($request->hasFile('upload')) {
        $validated = $request->validate([
          'upload' => 'required|image',
        ]);

        $fileName = $request->file('upload')->store('img', 'public');
        $fileUrl = Storage::disk('public')->url($fileName);

        //파일 저장 / 트랜잭션 추가하기
        BoardFiles::create([
          'user_email' => Auth::user()['email'],
          'file_name' => $fileName,
          'file_url' => $fileUrl,
        ]);
        DB::commit();

        return response()->json([
          'fileName' => $fileName,
          'uploaded' => 1,
          'url' => $fileUrl,
        ]);
      }
    } catch (\Exception $e) {
      DB::rollback();
      return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }
    return redirect()->back()->withErrors(['error' => 'no file']);
  }
}
