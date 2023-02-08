<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class BoardLike extends Model
{
  use SerializeDate;

  protected $table = 'board_like';
  protected $primaryKey = 'idx';

  protected $fillable = ['user_email', 'board_idx'];

  const CREATED_AT = 'created_at';
  const UPDATED_AT = null ;
}
