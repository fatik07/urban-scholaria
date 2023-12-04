<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratJenis extends Model
{
  use HasFactory;

  public $table = 'surat_jenis';

  protected $fillable = ['nama', 'deskripsi', 'gambar_alur_permohonan', 'gambar_service_level_aggreement'];

  public function suratSyarats()
  {
    return $this->hasMany(SuratSyarat::class, 'surat_jenis_id', 'id');
  }
}
