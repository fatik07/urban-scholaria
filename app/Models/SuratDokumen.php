<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SuratDokumen extends Model
{
  use HasFactory;

  public $table = 'surat_dokumen';

  protected $fillable = ['surat_id', 'surat_syarat_id', 'dokumen_upload'];
}
