<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ListChat extends Model
{
  use HasFactory;

  public $table = 'list_chat';

  protected $fillable = ['roomchat_id', 'account', 'message', 'read'];
}
