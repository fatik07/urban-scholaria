<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratSyarat extends Model
{
  use HasFactory;

  public $table = 'surat_syarat';

  protected $fillable = ['surat_jenis_id', 'nama'];

  public function suratJenis()
  {
    return $this->belongsTo(SuratJenis::class, 'surat_jenis_id');
  }
}
