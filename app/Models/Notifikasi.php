<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notifikasi extends Model
{
  use HasFactory;

  public $table = 'notifikasi';

  protected $fillable = ['user_id', 'judul', 'deskripsi', 'is_seen'];
}
