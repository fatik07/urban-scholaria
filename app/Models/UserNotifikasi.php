<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserNotifikasi extends Model
{
  use HasFactory;

  public $table = 'user_notifikasi';

  protected $fillable = ['user_id', 'onesignal_token'];
}
