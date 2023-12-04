<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
  use HasFactory;

  public $table = 'ulasan';

  protected $fillable = ['surat_id', 'isi'];

  public function surat()
  {
    return $this->belongsTo(Surat::class, 'surat_id');
  }
}
