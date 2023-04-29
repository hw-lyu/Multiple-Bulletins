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
    Schema::create('board_files', function (Blueprint $table) {
      $table->increments('idx')->comment('파일 번호');

      $table->string('user_email', 200)->nullable(false)->comment('유저 이메일');
      $table->string('file_name', 255)->nullable(false)->comment('파일이름(확장자명 포함)');
      $table->string('file_path', 255)->nullable(false)->comment('파일경로');

      $table->timestamp('file_upload_date')->useCurrent()->comment('파일 업로드 일');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('board_files');
  }
};
