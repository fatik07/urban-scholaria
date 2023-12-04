<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomChat extends Model
{
  use HasFactory;

  public $table = 'roomchat';

  protected $fillable = ['account', 'last_message', 'counter_satu', 'counter_kedua'];

  public function listChats()
  {
    return $this->hasMany(ListChat::class, 'roomchat_id');
  }
}
