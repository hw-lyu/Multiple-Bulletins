<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\UserRegisterController;

use App\Http\Controllers\JoinController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\LogoutController;

use App\Http\Controllers\AdminController;
use App\Http\Controllers\BoardController;
use App\Http\Controllers\UploadController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\PasswordController;

use App\Models\Board;
use App\Models\BoardTableList;

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

  $tableName = 'board';
  $board = (new Board)->setTable('board');
  $boardTableListData = BoardTableList::get();

  // $auth['grade'] - 1: 일반회원, 2: 관리자
  if (!empty($auth['grade']) && $auth['grade'] === 2) {
    $listData = $board->orderBy('idx', 'desc')
      ->paginate(3);
  } else {
    $listData = $board->where('board_state', 'n')
      ->orderBy('idx', 'desc')
      ->paginate(3);
  }

  return view('index', ['auth' => $auth, 'listData' => $listData, 'boardTableListData' => $boardTableListData, 'tableName' => $tableName]);
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
Route::get('/email/verify', [EmailController::class, 'notice'])->middleware('auth')->name('verification.notice');
Route::get('/email/verify/{id}/{hash}', [EmailController::class, 'verify'])->middleware(['auth', 'signed'])->name('verification.verify');
Route::post('/email/verification-notification', [EmailController::class, 'send'])->middleware(['auth', 'throttle:6,1'])->name('verification.send');

// 정보찾기 - 비밀번호 찾기
Route::get('/forgot-password', [PasswordController::class, 'index'])->middleware('guest')->name('password.request');
Route::post('/forgot-password', [PasswordController::class, 'emailSend'])->middleware('guest')->name('password.email');
Route::get('/reset-password/{token}', [PasswordController::class, 'reset'])->middleware('guest')->name('password.reset');
Route::post('/reset-password', [PasswordController::class, 'update'])->middleware('guest')->name('password.update');

Route::resources([
  'upload' => UploadController::class,
  'comments' => CommentController::class
]);

//Route::get('/board', [BoardController::class, 'index'])->name('board.index');
Route::get('/board/{tableName}/create', [BoardController::class, 'create'])->name('board.create');
Route::post('/board/{tableName}', [BoardController::class, 'store'])->name('board.store');
Route::get('/board/{tableName}/{idx}', [BoardController::class, 'show'])->name('board.show');
Route::get('/board/{tableName}/{idx}/edit', [BoardController::class, 'edit'])->name('board.edit');
Route::patch('/board/{tableName}/{idx}', [BoardController::class, 'update'])->name('board.update');
Route::delete('/board/{tableName}/{idx}', [BoardController::class, 'destroy'])->name('board.destroy');

Route::get('board/{tableName?}', function (string $tableName = null) {
  $boardTableListData = BoardTableList::get();
  if (!empty($tableName)) {
    $board = (new Board)->setTable($tableName);
  }

  return view('index', ['boardTableListData' => $boardTableListData]);
})->name('board.table');

Route::post('/board/like/{tableName}/{idx}', [BoardController::class, 'like'])->name('board.like');

// 코멘트
Route::post('/comments/{tableName}/{idx}/{offset}', [CommentController::class, 'list'])->name('comments.list');

// 어드민툴
Route::get('/admin', [AdminController::class, 'index'])->middleware('auth');
Route::get('/admin/board', [AdminController::class, 'index'])->middleware('auth')->name('admin.board');
Route::post('/admin/board/store', [AdminController::class, 'store'])->middleware('auth')->name('admin.board.store');
