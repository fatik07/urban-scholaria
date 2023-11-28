<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Otong;
use App\Models\SuratDokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
  public function cetakKwitansi($surat_id)
  {
    $surat = SuratDokumen::where('surat_id', $surat_id)->first();

    $nomor_surat = $surat->surat->id;

    $url = url("/api/surat?id_surat={$nomor_surat}");

    $qrcode = base64_encode(QrCode::format('svg')->size(200)->errorCorrection('H')->generate($url));

    $data = [
      'surat' => $surat,
      'qrcode' => $qrcode
    ];

    return Pdf::loadView('pdf.index', $data)->stream();
  }
}
