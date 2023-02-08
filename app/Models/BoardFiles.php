<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BoardFiles extends Model
{
  protected $table = 'board_files';
  protected $primaryKey = 'idx';
  public $timestamps = false;

  protected $fillable = ['user_email', 'file_name', 'file_url'];
}
