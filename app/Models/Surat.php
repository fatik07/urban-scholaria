<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
  use HasFactory;

  public $table = 'surat';

  protected $fillable = ['user_id', 'nama', 'status', 'is_ulasan', 'kategori', 'alamat_lokasi', 'longitude', 'latitude', 'jadwal_survey', 'nomor_penerbitan', 'is_dikembalikan', 'is_terlambat', 'alasan_dikembalikan'];

  public function suratDokumen()
  {
    return $this->hasMany(SuratDokumen::class, 'surat_id', 'id');
  }
}
