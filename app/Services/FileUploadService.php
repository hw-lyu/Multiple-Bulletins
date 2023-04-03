<?php

namespace App\Services;

use App\Repositories\FileUploadRepository;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class FileUploadService
{
  protected FileUploadRepository $fileUploadRepository;

  public function __construct(FileUploadRepository $fileUploadRepository)
  {
    $this->fileUploadRepository = $fileUploadRepository;
  }

  public function store(Request $request, array $data, array $createData)
  {
    DB::beginTransaction();

    try {
      if ($request->hasFile('upload')) {
        $validator = Validator::make($data, [
          'upload' => 'required|image'
        ])->validate();

        $fileName = $request->file('upload')->store('img', 'public');
        $fileUrl = Storage::disk('public')->url($fileName);

        $fileInfoData = [
          'file_name' => $fileName,
          'file_url' => $fileUrl,
          ...$createData
        ];

        $this->fileUploadRepository->create(data : $fileInfoData);

        DB::commit();

        return response()->json([
          'fileName' => $fileName,
          'uploaded' => 1,
          'url' => $fileUrl
        ]);
      }
    } catch (\Exception $e) {
      DB::rollback();

      Log::info($e->getMessage());
      return redirect()->back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->back()->withErrors(['error' => 'no file']);
  }

}
