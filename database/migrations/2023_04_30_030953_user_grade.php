<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('user_grade', function (Blueprint $table) {
      $table->increments('grade')->comment('등급 레벨');
      $table->string('grade_name', 50)->nullable(false)->comment('등급이름');
    });

    if (Schema::hasTable('user_grade')) {
      DB::table('user_grade')->insert([
        ['grade' => 1, 'grade_name' => '일반회원'],
        ['grade' => 2, 'grade_name' => '관리자']
      ]);
    }
  }

  /**
   * Reverse the migrations.
   *
   * @return void
   */
  public function down()
  {
    Schema::dropIfExists('user_grade');
  }
};
