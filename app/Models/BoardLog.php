<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class BoardLog extends Model
{
  use SerializeDate;

  protected $table = 'board_log';
  protected $primaryKey = 'idx';
  protected $fillable = ['board_idx', 'user_email', 'table_name', 'table_board_title', 'board_state'];

  const CREATED_AT = 'table_created_at';
  const UPDATED_AT = null;
}
