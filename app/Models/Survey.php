<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
    use HasFactory;

    public $table = 'survey';

    protected $fillable = ['surat_id', 'user_id', 'nama_survey', 'deskripsi_survey', 'jadwal_survey', 'tenggat_survey', 'status', 'foto_survey', 'alamat_survey', 'longitude', 'latitude', 'dokumen_surat_tugas', 'dokumen_survey', 'alasan_ditolak'];

    public function surat()
    {
        return $this->belongsTo(Surat::class, 'surat_id');
    }
}
