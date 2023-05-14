<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdminBoardUpdate extends FormRequest
{
  /**
   * Http/Controllers/AdminBoardController.php | update() rules
   * @return string[]
   */
  public function rules(): array
  {
    return [
      'board_idx' => 'required',
      'user_email' => 'required',
      'table_name' => 'required|regex:/^[a-z0-9]+$/',
      'table_board_title' => 'required|max:255',
      'board_cate' => 'required|array'
    ];
  }
}
