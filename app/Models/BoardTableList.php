<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class BoardTableList extends Model
{
  use SerializeDate;

  protected $table = 'board_table_list';
  protected $primaryKey = 'idx';
  protected $fillable = ['user_email', 'table_name', 'table_board_title', 'board_cate', 'table_order', 'board_state'];

  const CREATED_AT = 'table_created_at';
  const UPDATED_AT = 'table_updated_at';
}
