<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Password;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;

use App\Http\Controllers\UserRegisterController;

use App\Http\Controllers\JoinController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;
use App\Http\Controllers\FindInformationController;

use App\Http\Controllers\BoardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommentController;

use App\Models\Board;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//메인
Route::get('/', function () {
  $auth = Auth::user();

  // $auth['grade'] - 1: 일반회원, 2: 관리자
  if (!empty($auth['grade']) && $auth['grade'] === 2) {
    $listData = Board::orderBy('idx', 'desc')
      ->paginate(3);
  } else {
    $listData = Board::where('board_state', 'n')
      ->orderBy('idx', 'desc')
      ->paginate(3);
  }

  return view('index', ['auth' => $auth, 'listData' => $listData]);
})->name('home');

//로그인
Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login-check', [LoginController::class, 'authenticate'])->name('login.check');
//로그아웃
Route::post('/logout', [LogoutController::class, 'logout'])->name('logout');

//가입
Route::get('/join', [JoinController::class, 'index'])->name('join');
//가입등록
Route::post('/register', [UserRegisterController::class, 'userRegister'])->name('register');

//이메일 인증 관련
Route::get('/email/verify', function (Request $request) {

  //초깃값
  $userEmailVerifiedState = empty($request->user()->email_verified_at);

  return view('auth.verify-email', ['userEmailVerifiedState' => $userEmailVerifiedState]);
})->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
  $request->fulfill();

  return redirect('/');
})->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', function (Request $request) {
  $request->user()->sendEmailVerificationNotification();

  return back()->with('message', '확인 링크를 보냈습니다!<br>이메일을 확인해주세요!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 정보찾기 - 비밀번호 찾기
Route::get('/forgot-password', function () {
  return view('auth.forgot-password');
})->middleware('guest')->name('password.request');
Route::post('/forgot-password', function (Request $request) {
  $request->validate(['email' => 'required|email']);

  $status = Password::sendResetLink(
    $request->only('email')
  );

  return $status === Password::RESET_LINK_SENT
    ? back()->with(['status' => __($status)])
    : back()->withErrors(['email' => __($status)]);
})->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', function ($token, Request $request) {

  $email = $request->query('email');

  return view('auth.reset-password', ['token' => $token, 'email' => $email]);
})->middleware('guest')->name('password.reset');
Route::post('/reset-password', function (Request $request) {
  $request->validate([
    'token' => 'required',
    'email' => 'required|email',
    'password' => 'required|min:8|confirmed',
  ]);

  $status = Password::reset(
    $request->only('email', 'password', 'password_confirmation', 'token'),
    function ($user, $password) {
      $user->forceFill([
        'password' => Hash::make($password)
      ])->setRememberToken(Str::random(60));

      $user->save();

      event(new PasswordReset($user));
    }
  );

  return $status === Password::PASSWORD_RESET
    ? redirect()->route('login')->with('status', __($status))
    : back()->withErrors(['email' => [__($status)]]);
})->middleware('guest')->name('password.update');

Route::resources([
  'boards' => BoardController::class,
  'upload' => UploadController::class,
  'comments' => CommentController::class,
]);

Route::post('boards/like/{idx}', [BoardController::class, 'like'])->name('boards.like');
Route::post('comments/{idx}/{offset}', [CommentController::class, 'list'])->name('comments.list');
