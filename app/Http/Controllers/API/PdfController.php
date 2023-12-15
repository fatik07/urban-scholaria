<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\SuratDokumen;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class PdfController extends Controller
{
    public function trackingSurat(Request $request)
    {
        try {
            $user = auth()->user();
            $id_surat = $request->query('id_surat');

            if ($id_surat) {
                $query = Surat::with('suratJenis', 'user', 'suratDokumen.suratSyarat')
                    ->where('id', $id_surat)
                    ->where('user_id', $user->id);

                $surat = $query->first();

                if (!$surat) {
                    return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan atau Anda tidak memiliki hak akses'], 404);
                }

                return response()->json(['success' => true, 'message' => 'Surat berhasil di tracking dengan id surat ' . $id_surat, 'data' => $surat]);
            } else {
                return response()->json(['success' => false, 'message' => 'Id surat tidak ditemukan'], 404);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cetakKwitansi($surat_id)
    {
        $surat = Surat::where('id', $surat_id)->first();
//        $surat = SuratDokumen::where('surat_id', $surat_id)->first();

        if (!$surat) {
            return response()->json(['success' => false, 'message' => 'Surat tidak ditemukan']);
        }

//        $nomor_surat = $surat->surat->id;

        $url = url("/api/tracking-surat?id_surat={$surat->id}");
//        $url = url("/api/surat?id_surat={$nomor_surat}");

        $qrcode = base64_encode(QrCode::format('svg')->size(400)->errorCorrection('H')->generate($url));

        $data = [
            'surat' => $surat,
            'qrcode' => $qrcode
        ];

        return Pdf::loadView('pdf.index', $data)->stream();
    }

    public function cetakSurat($surat_id)
    {
        try {
            $surat = Surat::with('suratDokumen.suratSyarat.suratJenis')->findOrFail($surat_id);

            if ($surat->status !== 'Selesai') {
                return response()->json(['message' => 'Surat tidak dapat dicetak karena status belum selesai'], 400);
            }

            // Get the SuratDokumen for the specified Surat
            $suratDokumen = SuratDokumen::where('surat_id', $surat_id)->get();

            // Initialize empty arrays for suratSyarat and suratJenis
            $suratSyarat = [];
            $suratJenis = [];

            // Loop through each SuratDokumen and retrieve suratSyarat and suratJenis
            foreach ($suratDokumen as $dokumen) {
                $suratSyarat[] = $dokumen->suratSyarat;
                $suratJenis[] = $dokumen->suratSyarat->suratJenis;
            }

            $data = [
                'surat' => $surat,
                'suratDokumen' => $suratDokumen,
                'suratSyarat' => $suratSyarat,
                'suratJenis' => $suratJenis,
            ];

            $pdf = PDF::loadView('pdf.cetak-surat', $data);

            return $pdf->stream("surat_{$surat->id}.pdf");
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function cetakLegalitas($surat_id)
    {
        try {
            $surat = Surat::with('suratDokumen.suratSyarat.suratJenis')->findOrFail($surat_id);

            if ($surat->status !== 'Selesai') {
                return response()->json(['message' => 'Surat tidak dapat dicetak karena status belum selesai'], 400);
            }

            // Get the SuratDokumen for the specified Surat
            $suratDokumen = SuratDokumen::where('surat_id', $surat_id)->get();

            // Initialize empty arrays for suratSyarat and suratJenis
            $suratSyarat = [];
            $suratJenis = [];

            // Loop through each SuratDokumen and retrieve suratSyarat and suratJenis
            foreach ($suratDokumen as $dokumen) {
                $suratSyarat[] = $dokumen->suratSyarat;
                $suratJenis[] = $dokumen->suratSyarat->suratJenis;
            }

            $data = [
                'surat' => $surat,
                'suratDokumen' => $suratDokumen,
                'suratSyarat' => $suratSyarat,
                'suratJenis' => $suratJenis,
            ];

            $pdf = PDF::loadView('pdf.cetak-legalitas', $data);

            return $pdf->stream("surat_legalitas_{$surat->id}.pdf");
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
