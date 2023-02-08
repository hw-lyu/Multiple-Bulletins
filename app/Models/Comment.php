<?php

namespace app\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\SerializeDate;

class Comment extends Model
{
  use SerializeDate;

  protected $table = 'comment';
  protected $primaryKey = 'idx';

  protected $fillable = ['board_idx', 'comment_writer', 'comment_content', 'parent_idx', 'comment_state'];

  const CREATED_AT = 'comment_created_at';
  const UPDATED_AT = 'comment_updated_at';
}
