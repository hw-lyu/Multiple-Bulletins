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
    Schema::create('comment_basic', function (Blueprint $table) {
      $table->increments('idx')->comment('코멘트 번호');
      $table->string('comment_writer', 200)->nullable(false)->comment('코멘트작성자');
      $table->mediumText('comment_content')->comment('코멘트 내용(html tag 포함)');

      $table->timestamp('comment_created_at')->useCurrent()->comment('코멘트 작성일');
      $table->timestamp('comment_updated_at')->useCurrentOnUpdate()->nullable()->comment('코멘트 변경일');

      $table->text('depth_idx')->comment('부모의 idx-내idx를 기준으로 text 저장 (부모 idx가 없으면 내 코멘트의 idx만 저장)');
      $table->unsignedInteger('group_idx')->comment('댓글 Root 번호 (최상위 부모 값)');
      $table->unsignedInteger('group_order')->comment('부모 코멘트 번호, 댓글 및 대댓글 순서 (루트기준)');

      $table->enum('comment_state', ['n', 'y'])->default('n')->comment('코멘트 삭제 여부');
    });
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('comment_basic');
  }
};
