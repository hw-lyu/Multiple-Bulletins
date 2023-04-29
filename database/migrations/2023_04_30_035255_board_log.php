<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('board_log', function (Blueprint $table) {
      $table->increments('idx')->comment('게시판 로그 번호');
      $table->unsignedInteger('board_idx')->nullable(false)->comment('게시판 고유 번호');

      $table->string('user_email', 200)->nullable(false)->comment('수정한 유저 이메일');
      $table->string('table_name', 200)->nullable(false)->comment('변경된 테이블 이름');
      $table->string('table_board_title', 255)->nullable(false)->comment('변경된 게시판 테이블 이름');

      $table->timestamp('table_created_at')->useCurrent()->comment('테이블 생성일자');

      $table->enum('board_state', ['n', 'y'])->default('n')->comment('게시판 공개/비공개 여부');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('board_log');
  }
};
