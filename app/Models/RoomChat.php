<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomChat extends Model
{
  use HasFactory;

  public $table = 'room_chat';

  protected $fillable = ['account', 'last_message', 'counter_satu', 'counter_kedua'];
}
