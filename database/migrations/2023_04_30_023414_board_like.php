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
    Schema::create('board_like', function (Blueprint $table) {
      $table->increments('idx')->comment('좋아요 번호');
      $table->string('user_email', 200)->nullable(false)->comment('유저이메일');
      $table->unsignedInteger('board_idx')->nullable(false)->comment('게시글 번호');
      $table->timestamp('created_at')->useCurrent()->comment('좋아요 등록일');

      $table->unique(['user_email', 'board_idx']);
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('board_like');
  }
};
