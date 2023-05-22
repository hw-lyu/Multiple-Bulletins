<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use App\Services\UserService;

use Exception;

class LoginController extends Controller
{

  protected UserService $userService;

  public function __construct(UserService $userService)
  {
    $this->userService = $userService;
  }

  public function index()
  {
    return redirect()->route('home');
  }

  public function authenticate(Request $request)
  {
    $data = $request->input();

    try {
      $result = $this->userService->login(request: $request, data: $data);

      if (gettype($result) === 'array' && !empty($result['email'])) {
        throw new Exception($result['email']);
      }
    } catch (Exception $e) {
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return $result;
  }

  public function naverLogin()
  {
    $client_id = env('NAVER_CLIENT_ID');
    $redirectURI = urlencode("http://localhost/member/Oauth2C");
    $state = "RAMDOM_STATE";
    $apiURL = "https://nid.naver.com/oauth2.0/authorize?response_type=code&client_id=" . $client_id . "&redirect_uri=" . $redirectURI . "&state=" . $state;

    return redirect()->away($apiURL);
  }

  public function naverCallBack()
  {
    $client_id = env('NAVER_CLIENT_ID');
    $client_secret = env('NAVER_CLIENT_SECRET');
    $code = $_GET["code"];
    $state = $_GET["state"];
    $redirectURI = urlencode("http://localhost/member/Oauth2C");
    $url = "https://nid.naver.com/oauth2.0/token?grant_type=authorization_code&client_id=" . $client_id . "&client_secret=" . $client_secret . "&redirect_uri=" . $redirectURI . "&code=" . $code . "&state=" . $state;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = array();
    $res = curl_exec($ch);
    $status_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($status_code == 200) {
      $responseArr = json_decode($res, true);
      $encryptedToken = encrypt($responseArr['refresh_token']);

      $apiUrl = 'https://openapi.naver.com/v1/nid/me';

      $ch2 = curl_init($apiUrl);
      curl_setopt($ch2, CURLOPT_HTTPHEADER, [
        'Authorization: ' . $responseArr['token_type'] . ' ' . $responseArr['access_token'],
        'Content-Type: application/json',
        'Accept: application/json',
        'expires: ' . $responseArr['expires_in']
      ]);
      curl_setopt($ch2, CURLOPT_RETURNTRANSFER, true);

      $res2 = curl_exec($ch2);
      curl_close($ch2);

      $resArr = json_decode($res2, true);

      if ($resArr['message'] === 'success') {
        return response()->view('member.join', ['socialArr' => $resArr['response'], 'socialType' => 'naver']);
      }

      return back()->withErrors(['error' => '다시 시도해주세요.' . $resArr["message"]]);
    } else {
      echo "Error 내용:" . $res;
    }
  }
}
