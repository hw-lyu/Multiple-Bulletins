<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // \App\Models\User::factory(10)->create();

    // \App\Models\User::factory()->create([
    //     'name' => 'Test User',
    //     'email' => 'test@example.com',
    // ]);

    for ($i = 1; $i <= 300; $i++) {
      DB::table('comment')->insert([
        'board_idx' => 76,
        'comment_writer' => Str::random(10) . '@gmail.com',
        'comment_content' => Str::random(50),
        'parent_idx' => '31'
      ]);
    }
  }
}
