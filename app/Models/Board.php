<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class Board extends Model
{
  use SerializeDate;

  protected $table = 'board_basic';
  protected $primaryKey = 'idx';
  protected $fillable = ['user_email', 'board_cate', 'board_title', 'views', 'view_like', 'board_content', 'photo_state', 'all_comment'];

  const CREATED_AT = 'view_created_at';
  const UPDATED_AT = 'view_updated_at';
}
