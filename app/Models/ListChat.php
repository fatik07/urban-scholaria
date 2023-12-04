<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListChat extends Model
{
  use HasFactory;

  public $table = 'listchat';

  protected $fillable = ['roomchat_id', 'account', 'message', 'read'];

  public function roomChat()
  {
    return $this->belongsTo(RoomChat::class, 'roomchat_id');
  }
}
