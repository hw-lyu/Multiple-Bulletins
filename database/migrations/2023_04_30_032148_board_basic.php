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
    Schema::create('board_basic', function (Blueprint $table) {
      $table->increments('idx')->comment('글번호');
      $table->string('user_email', 200)->nullable(false)->comment('유저이메일');
      $table->string('board_cate', 50)->nullable(false)->comment('글분류');
      $table->string('board_title', 100)->nullable(false)->comment('글제목');
      $table->string('writer', 50)->nullable(false)->comment('글작성자');

      $table->timestamp('view_created_at')->useCurrent()->comment('글 작성일');
      $table->timestamp('view_updated_at')->useCurrentOnUpdate()->nullable()->comment('글 변경일');

      $table->mediumText('board_content')->comment('글내용 (html tag 포함)');
      $table->unsignedInteger('views')->default(0)->comment('조회수');
      $table->unsignedInteger('view_like')->default(0)->comment('추천수');
      $table->unsignedInteger('all_comment')->default(0)->comment('게시물 총 코멘트수');

      $table->enum('photo_state', ['n', 'y'])->default('n')->comment('사진 여부');
      $table->enum('board_state', ['n', 'y'])->default('n')->comment('게시글 삭제 여부');

      $table->timestamp('deleted_at')->comment('글 삭제일');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('board_basic');
  }
};
