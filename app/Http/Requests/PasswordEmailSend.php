<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class PasswordEmailSend extends FormRequest
{
  /**
   * Http/Controllers/PasswordController.php | emailSend() rules
   * @return array
   */
  public function rules(): array
  {
    return ['email' => 'required|email'];
  }

  /**
   * 유효성 감사 속성 값
   * @return array
   */
  public function attributes() : array
  {
    return [
      "email" => "게시판 이메일 주소"
    ];
  }

  /**
   * 유효성검사 메시지
   * @return array
   */
  public function messages(): array
  {
    return [
      'email' => ':attribute룰 다시 확인해주세요.'
    ];
  }
}
