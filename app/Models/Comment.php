<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class Comment extends Model
{
  use SerializeDate;

  protected $table = 'comment_basic';
  protected $primaryKey = 'idx';

  protected $fillable = ['board_idx', 'comment_writer', 'comment_content', 'comment_state', 'depth_idx',' group_idx', 'group_order'];

  const CREATED_AT = 'comment_created_at';
  const UPDATED_AT = 'comment_updated_at';
}
