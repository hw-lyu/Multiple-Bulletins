<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class BoardTableList extends Model
{
  use SerializeDate;

  protected $table = 'board_table_list';
  protected $primaryKey = 'idx';
  protected $fillable = ['user_email', 'table_name', 'table_board_title', 'table_order'];

  const CREATED_AT = 'table_created_at';
  const UPDATED_AT = null;
}
