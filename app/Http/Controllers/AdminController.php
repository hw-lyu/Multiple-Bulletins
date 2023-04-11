<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use App\Models\BoardTableList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;

use Exception;

class AdminController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index()
  {
    return view('admin.index');
  }

  public function store(Request $request)
  {
    $data = $request->input();

    $validator = Validator::make($data, [
      'board_url' => 'required|regex:/[a-z0-9]/',
      'board_title' => 'required|max:255',
    ])->validate();

    $userEmail = Auth::user()['email'];
    $userId = explode('@', $userEmail)[0];
    $tableName = $userId . '_' . $validator['board_url'];

    /*
    * CREATE TABLE 문과 트랜잭션 분리
    * CREATE TABLE 등은 암시적 커밋을 유발하는 문
    * 이는 트랜잭션이 닫힐 때 롤백이 불가능하기 때문에 RefreshDatabase 특성이 작동하지 않음을 의미
    * https://stackoverflow.com/questions/67198158/there-is-no-active-transaction-when-refreshing-database-in-laravel-8-0-test
    * */

    DB::beginTransaction();

    try {
      BoardTableList::create([
        'user_email' => $userEmail,
        'table_name' => $tableName,
        'table_board_title' => $validator['board_title']
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    if (!Schema::hasTable('board_' . $tableName)) {
      // board와 comment는 1짝씩 커플이므로 같이 생성된다.
      DB::statement('Create Table IF NOT EXISTS ' . 'board_' . $tableName . ' like board');
      DB::statement('Create Table IF NOT EXISTS ' . 'comment_' . $tableName . ' like comment');
    }

    return redirect()->route('admin.board')->with('message', '게시판이 등록되었습니다!');
  }
}
