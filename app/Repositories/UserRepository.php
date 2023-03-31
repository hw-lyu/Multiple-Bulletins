<?php

namespace App\Repositories;

use App\Interfaces\UserRepositoryInterface;
use App\Models\User;

class UserRepository implements UserRepositoryInterface
{

  protected $user;

  public function __construct(User $user)
  {
    $this->user = $user;
  }

  public function create(array $data = [])
  {
    return $this->user->create($data);
  }

}
