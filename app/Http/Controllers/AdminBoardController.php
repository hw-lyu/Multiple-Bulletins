<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

use App\Models\BoardLog;
use App\Models\BoardTableList;

use App\Http\Requests\AdminBoardStore;
use App\Http\Requests\AdminBoardUpdate;

use Exception;

class AdminBoardController extends Controller
{
  public function index()
  {
    $listData = BoardTableList::orderBy('idx', 'desc')
      ->paginate(10);

    return view('admin.board.index', ['listData' => $listData]);
  }

  public function create()
  {
    return view('admin.board.create');
  }

  public function store(AdminBoardStore $adminBoardStore)
  {

    $validator = $adminBoardStore->all();

    $userEmail = Auth::user()['email'];
    $tableName = $validator['board_url'];
    $boardCate = implode('|', $validator['board_cate']);

    /*
    * CREATE TABLE 문과 트랜잭션 분리
    * CREATE TABLE 등은 암시적 커밋을 유발하는 문
    * 이는 트랜잭션이 닫힐 때 롤백이 불가능하기 때문에 RefreshDatabase 특성이 작동하지 않음을 의미
    * https://stackoverflow.com/questions/67198158/there-is-no-active-transaction-when-refreshing-database-in-laravel-8-0-test
    * */

    DB::beginTransaction();

    try {
      $boardTabList = BoardTableList::create([
        'user_email' => $userEmail,
        'table_name' => $tableName,
        'table_board_title' => $validator['board_title'],
        'board_cate' => $boardCate
      ]);

      BoardLog::create([
        'board_idx' => $boardTabList['idx'],
        'user_email' => $boardTabList['user_email'],
        'table_name' => $boardTabList['table_name'],
        'table_board_title' => $boardTabList['table_board_title'],
        'table_created_at' => $boardTabList['table_created_at'],
        'board_cate' => $boardCate
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    if (!Schema::hasTable('board_' . $tableName)) {
      // board와 comment는 1짝씩 커플이므로 같이 생성된다.
      DB::statement('Create Table IF NOT EXISTS ' . 'board_' . $tableName . ' like board_basic');
      DB::statement('Create Table IF NOT EXISTS ' . 'comment_' . $tableName . ' like comment_basic');
    }

    return redirect()->route('admin.board')->with('message', '게시판이 등록되었습니다!');
  }

  public function edit(string $boardIdx)
  {
    $listData = BoardTableList::find($boardIdx);
    $logData = BoardLog::where('board_idx', $boardIdx)->orderBy('idx', 'desc')->get();
    $cateData = explode('|', $listData['board_cate']);

    return view('admin.board.edit', ['listData' => $listData, 'cateData' => $cateData, 'logData' => $logData]);
  }

  public function update(string $boardIdx, Request $request, AdminBoardUpdate $adminBoardUpdate)
  {
    $data = $request->all();
    $validator = $adminBoardUpdate->all();

    $boardCate = implode('|', $validator['board_cate']);

    DB::beginTransaction();

    try {
      BoardTableList::find($boardIdx)->
      update([
        'user_email' => $data['user_email'],
        'table_name' => $data['table_name'],
        'table_board_title' => $data['table_board_title'],
        'table_created_at' => $data['table_created_at'],
        'board_cate' => $boardCate
      ]);

      BoardLog::create([
        'board_idx' => $data['board_idx'],
        'user_email' => $data['user_email'],
        'table_name' => $data['table_name'],
        'table_board_title' => $data['table_board_title'],
        'table_created_at' => $data['table_created_at'],
        'board_cate' => $boardCate
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    if (!Schema::hasTable('board_' . $data['table_name'])) {
      // board와 comment는 1짝씩 커플이므로 같이 수정한다.
      DB::rollback();
      DB::statement(
        'RENAME TABLE ' . 'board_' . $data['old_table_name'] . ' TO ' . 'board_' . $data['table_name'] .
        ', ' . 'comment_' . $data['old_table_name'] . ' TO ' . 'comment_' . $data['table_name']
      );
    }

    return redirect()->route('admin.board.edit', ['boardIdx' => $data['board_idx']])->with('message', '게시판이 수정되었습니다!');
  }

  public function destroy(string $boardIdx)
  {
    // 비공개로 변경될시 해당 게시판 비공개 및 파일, 글은 그대로 보존하는 형식으로 진행
    $list = BoardTableList::find($boardIdx);
    $userEmail = Auth::user()['email'];
    $listBoardState = $list['board_state'] === 'n' ? 'y' : 'n';
    DB::beginTransaction();

    try {
      $list->update([
        'board_state' => $listBoardState
      ]);

      BoardLog::create([
        'user_email' => $userEmail,
        'board_idx' => $list['idx'],
        'table_name' => $list['table_name'],
        'table_board_title' => $list['table_board_title'],
        'board_cate' => $list['board_cate'],
        'board_state' => $listBoardState
      ]);

      DB::commit();
    } catch (Exception $e) {
      DB::rollback();
      return back()->withErrors(['error' => $e->getMessage()]);
    }

    return redirect()->route('admin.board')->with('message', '게시판이 ' . ($listBoardState === 'n' ? '공개 ' : '비공개') . ' 상태로 변경되었습니다!');
  }
}
