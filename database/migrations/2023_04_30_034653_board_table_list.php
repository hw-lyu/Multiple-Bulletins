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
    Schema::create('board_table_list', function (Blueprint $table) {
      $table->increments('idx')->comment('테이블 리스트 번호');

      $table->string('user_email', 200)->nullable(false)->comment('생성한 유저이메일');
      $table->string('table_name', 200)->nullable(false)->comment('게시판 테이블 생성 이름');
      $table->string('table_board_title', 255)->nullable(false)->comment('게시판 이름');

      $table->timestamp('table_created_at')->useCurrent()->comment('테이블 생성일자');
      $table->timestamp('table_updated_at')->useCurrentOnUpdate()->nullable()->comment('테이블 수정일자');

      $table->unsignedInteger('table_order')->default(0)->comment('게시판 순서 (추후 기능을 위해 미리 추가)');

      $table->enum('board_state', ['n', 'y'])->default('n')->comment('게시판 공개/비공개 여부');

      $table->unique('table_name');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('board_table_list');
  }
};
