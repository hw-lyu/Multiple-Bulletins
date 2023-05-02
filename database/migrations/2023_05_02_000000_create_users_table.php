<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

return new class extends Migration {
  /**
   * Run the migrations.
   *
   * @return void
   */
  public function up()
  {
    Schema::create('users', function (Blueprint $table) {
      $table->id();
      $table->string('name');
      $table->string('email')->unique();
      $table->timestamp('email_verified_at')->nullable();
      $table->string('password');
      $table->rememberToken();
      $table->unsignedInteger('grade')->default(1)->comment('유저등급');
      $table->timestamps();

      $table->foreign('grade')->references('grade')->on('user_grade');
    });

    if (Schema::hasTable('users')) {
      DB::table('users')->insert([
        ['id' => 1, 'name' => '테스트', 'email' => 'test@test', 'password' => Hash::make(123), 'grade' => 2],
        ['id' => 2, 'name' => '테스트2', 'email' => 'test2@test', 'password' => Hash::make(123), 'grade' => 2]
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
    Schema::dropIfExists('users');
  }
};
