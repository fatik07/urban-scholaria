<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
  use HasFactory;

  public $table = 'survey';

  protected $fillable = ['surat_id', 'user_id', 'jadwal_survey', 'status', 'foto_survey', 'alamat_survey', 'longitude', 'latitude', 'dokumen_survey', 'alasan_ditolak'];
}
