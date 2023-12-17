<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Surat extends Model
{
    use HasFactory;

    public $table = 'surat';

    protected $fillable = ['user_id', 'surat_jenis_id', 'nama', 'status', 'is_ulasan', 'kategori', 'alamat_lokasi', 'longitude', 'latitude', 'jadwal_survey', 'nomor_penerbitan', 'is_dikembalikan', 'is_terlambat', 'alasan_dikembalikan', 'alasan_ditolak'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function suratJenis()
    {
        return $this->belongsTo(SuratJenis::class, 'surat_jenis_id');
    }

    public function suratDokumen()
    {
        return $this->hasMany(SuratDokumen::class, 'surat_id', 'id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updating(function ($surat) {
            if ($surat->isDirty('status') && $surat->status === 'Selesai') {
                $surat->nomor_penerbitan = $surat->generateNomorPenerbitan();
            }
        });
    }

    public function generateNomorPenerbitan()
    {
        $lastNomorPenerbitan = static::where('status', 'Selesai')
            ->max('nomor_penerbitan');

        // Pemisahan nomor penerbitan untuk mendapatkan angka terakhir
        $lastNumber = intval(explode('/', $lastNomorPenerbitan)[0] ?? 0);

        return sprintf('%02d/DP/%d', $lastNumber + 1, now()->year);
    }
}
