<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBoardStore extends FormRequest
{
  /**
   * Http/Controllers/AdminBoardController.php | store() rules
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'board_url' => 'required|regex:/^[a-z0-9]+$/',
      'board_title' => 'required|max:255',
      'board_cate' => 'required|array'
    ];
  }
}
