<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
  use RefreshDatabase;

  /**
   * A basic test example.
   *
   * @return void
   */
  public function test_the_application_returns_a_successful_response(): void
  {
    $response = $this->get('/');

    $response->assertStatus(200);
  }

  public function test_관리자_로그인_안되있을시_어드민_접속(): void
  {
    $response = $this->get('/admin');

    $response->assertStatus(302);
  }
}
