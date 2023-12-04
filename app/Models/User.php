<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
  use HasApiTokens, HasFactory, Notifiable;

  public $table = 'user';

  protected $fillable = [
    'role_id',
    'username',
    'email',
    'password',
    'nama_lengkap',
    'foto',
    'ktp',
    'jenis_identitas',
    'nomor_identitas',
    'jenis_kelamin',
    'tempat_lahir',
    'tanggal_lahir',
    'provinsi',
    'kabupaten_kota',
    'kecamatan',
    'kelurahan',
    'alamat',
    'no_telp',
    'pekerjaan',
    'is_login',
    'is_active'
  ];

  protected $hidden = [
    'password',
    // 'remember_token',
  ];

  public function role()
  {
    return $this->belongsTo(Role::class, 'role_id');
  }
}
