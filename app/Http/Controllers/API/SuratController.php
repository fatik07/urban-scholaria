<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Surat;
use App\Models\SuratDokumen;
use App\Models\SuratJenis;
use App\Models\SuratSyarat;
use App\Models\Survey;
use Auth;
use Carbon\Carbon;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuratController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            // if (auth()->user()->role->nama !== 'Pemohon') {
            //   $suratId = $request->query('id_surat');
            //   $status = $request->query('status');
            //   $nama = $request->query('nama');
            //   $kategori = $request->query('kategori');
            //   $order = $request->query('order_by');

            //   if ($suratId || $status || $nama || $kategori || $order) {
            //     $query = Surat::with('suratDokumen.suratSyarat.suratJenis');
            //     $message = "Surat berhasil didapatkan";

            //     if ($suratId) {
            //       // $query->where('id', $suratId);
            //       $query->join('surat_dokumen', 'surat.id', '=', 'surat_dokumen.surat_id')
            //         ->join('surat_syarat', 'surat_dokumen.surat_syarat_id', '=', 'surat_syarat.id')
            //         ->join('surat_jenis', 'surat_syarat.surat_jenis_id', '=', 'surat_jenis.id')
            //         ->select('surat.*', 'surat_jenis.nama as surat_jenis_nama')
            //         ->where('surat.id', $suratId);
            //       $message .= " dengan id " . $suratId;
            //     }

            //     if ($status) {
            //       $query->where('status', $status);
            //       $message .= " dengan status " . $status;
            //     }

            //     if ($nama) {
            //       $query->whereHas('suratDokumen.suratSyarat.suratJenis', function ($query) use ($nama) {
            //         $query->where('nama', 'like', "%$nama%");
            //       });
            //       $message .= " dengan nama " . $nama;
            //     }

            //     if ($kategori) {
            //       $query->where('kategori', $kategori);
            //       $message .= " dengan kategori " . $kategori;
            //     }

            //     if ($order) {
            //       $query->orderBy('created_at', $order);
            //       $message .= " dengan order by " . $order;
            //     }

            //     $surat = $query->get();

            //     if ($surat->isEmpty()) {
            //       return response()->json(['success' => false, 'message' => 'Maaf, nilai parameter tidak sesuai']);
            //     }

            //     return response()->json(['success' => true, 'message' => $message, 'data' => $surat]);
            //   } else {
            //     $surat = Surat::with('suratDokumen.suratSyarat.suratJenis')->get();

            //     return response()->json(['success' => true, 'message' => 'Semua surat berhasil didapatkan', 'data' => $surat]);
            //   }
            // } else {
            //   $suratId = $request->query('id_surat');
            //   $status = $request->query('status');
            //   $nama = $request->query('nama');
            //   $kategori = $request->query('kategori');
            //   $order = $request->query('order_by', 'asc');

            //   $query = Surat::with('suratDokumen.suratSyarat.suratJenis');
            //   $message = "Surat berhasil didapatkan";

            //   if ($status || $nama || $kategori || $order) {
            //     if (!$suratId) {
            //       return response()->json(['message' => 'Akses ditolak'], 403);
            //     }
            //   }

            //   if ($suratId) {
            //     // $query->where('id', $suratId);
            //     $query->join('surat_dokumen', 'surat.id', '=', 'surat_dokumen.surat_id')
            //       ->join('surat_syarat', 'surat_dokumen.surat_syarat_id', '=', 'surat_syarat.id')
            //       ->join('surat_jenis', 'surat_syarat.surat_jenis_id', '=', 'surat_jenis.id')
            //       ->select('surat.*', 'surat_jenis.nama as surat_jenis_nama')
            //       ->where('surat.id', $suratId);
            //     $message .= " dengan id " . $suratId;
            //   }

            //   $surat = $query->get();

            //   return response()->json(['success' => true, 'message' => $message, 'data' => $surat]);
            // }
            $suratId = $request->query('id_surat');
            $status = $request->query('status');
            $nama = $request->query('nama');
            $kategori = $request->query('kategori');
            $order = $request->query('order_by');
            $suratJenisId = $request->query('surat_jenis_id');

            if ($suratId || $status || $nama || $kategori || $order || $suratJenisId) {
                $query = Surat::with('suratJenis', 'user', 'suratDokumen.suratSyarat');
                $message = "Surat berhasil didapatkan";

                if ($suratId) {
                    $query->where('id', $suratId);
                    $message .= " dengan id " . $suratId;
                }

                if ($status) {
                    $query->where('status', $status);
                    $message .= " dengan status " . $status;
                }

                if ($nama) {
                    $query->whereHas('suratJenis', function ($query) use ($nama) {
                        $query->where('surat_jenis.nama', 'like', "%$nama%");
                    });
                    $message .= " dengan nama " . $nama;
                }

                if ($kategori) {
                    $query->where('kategori', $kategori);
                    $message .= " dengan kategori " . $kategori;
                }

                if ($order) {
                    $query->orderBy('created_at', $order);
                    $message .= " dengan order by " . $order;
                }

                if ($suratJenisId) {
                    $query->where('surat_jenis_id', $suratJenisId);
                    $message .= " dengan surat jenis id " . $suratJenisId;
                }

                $surat = $query->get();

                if ($surat->isEmpty()) {
                    return response()->json(['success' => false, 'message' => 'Maaf, nilai parameter tidak sesuai', 'data' => null]);
                }

                return response()->json(['success' => true, 'message' => $message, 'data' => $surat]);
            } else {
                $surat = Surat::with('suratJenis', 'user', 'suratDokumen.suratSyarat')->get();

                return response()->json(['success' => true, 'message' => 'Semua surat berhasil didapatkan', 'data' => $surat]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        try {
            if (auth()->user()->role->nama !== 'Pemohon') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                "user_id" => "nullable|exists:user,id",
                "surat_jenis_id" => "nullable|exists:surat_jenis,id",
                "nama" => "nullable|string|max:255",
                "status" => "nullable|in:Pengisian Dokumen,Verifikasi Operator,Verifikasi Verifikator,Penjadwalan Survey,Verifikasi Hasil Survey,Verifikasi Kepala Dinas,Selesai,Ditolak",
                "is_ulasan" => "nullable|in:Y,N",
                "kategori" => "nullable|in:TK,SD,SMP,SMA,SMK",
                "alamat_lokasi" => "nullable|string|max:255",
                "longitude" => "nullable|string|max:255",
                "latitude" => "nullable|string|max:255",
                "jadwal_survey" => "nullable|date",
                "nomor_penerbitan" => "nullable|string|max:255",
                "is_dikembalikan" => "nullable|in:Y,N",
                "is_terlambat" => "nullable|in:Y,N",
                "alasan_dikembalikan" => "nullable|string|max:255",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            };

            $surat = Surat::create([
                "user_id" => Auth::user()->id,
                "surat_jenis_id" => $request->surat_jenis_id,
                "nama" => $request->nama,
                "status" => 'Pengisian Dokumen',
                "is_ulasan" => 'N',
                "kategori" => $request->kategori,
                "alamat_lokasi" => $request->alamat_lokasi,
                "longitude" => $request->longitude,
                "latitude" => $request->latitude,
                "jadwal_survey" => null,
                "nomor_penerbitan" => null,
                "is_dikembalikan" => 'N',
                "is_terlambat" => 'N',
                "alasan_dikembalikan" => null
            ]);

            return response()->json([
                "success" => true,
                "data" => $surat,
                "message" => "Surat berhasil dibuat",
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $surat = Surat::find($id);

            if (!$surat) {
                return response()->json(['message' => 'Surat tidak ditemukan'], 404);
            }

            // if ($surat->is_dikembalikan === 'Y') {
            $validator = Validator::make($request->all(), [
                "kategori" => "nullable|in:TK,SD,SMP,SMA,SMK",
                "alamat_lokasi" => "nullable|string|max:255",
                "longitude" => "nullable|string|max:255",
                "latitude" => "nullable|string|max:255",
                "is_dikembalikan" => "nullable|in:Y,N",
                "is_terlambat" => "nullable|in:Y,N",
                "alasan_dikembalikan" => "nullable|string|max:255",
                'dokumen_upload' => 'nullable|mimes:pdf,doc,docx',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $is_dikembalikan = 'N';
            $is_terlambat = 'N';
            $alasan_dikembalikan = null;

            $dataToUpdate = $request->only(['kategori', 'alamat_lokasi', 'longitude', 'latitude', 'is_dikembalikan', 'is_terlambat', 'alasan_dikembalikan']);
            $dataToUpdate['is_dikembalikan'] = $is_dikembalikan;
            $dataToUpdate['is_terlambat'] = $is_terlambat;
            $dataToUpdate['alasan_dikembalikan'] = $alasan_dikembalikan;

            $surat->update($dataToUpdate);

            return response()->json(['success' => true, 'message' => 'Surat berhasil diupdate', 'data' => $surat]);
            // } else {
            //   return response()->json(['success' => false, 'message' => 'Tidak bisa update surat, karena is_dikembalikan masih false']);
            // }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $surat = Surat::findOrFail($id);

            if (!$surat) {
                return response()->json(['message' => 'Surat tidak ditemukan'], 404);
            }

            $surat->delete();

            return response()->json(['success' => true, 'message' => 'Surat berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSuratByUserId($userId)
    {
        try {
            if (auth()->user()->id != $userId) {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::with('suratJenis', 'user', 'suratDokumen.suratSyarat')->where('user_id', $userId)->get();

            if ($surat->isNotEmpty()) {
                return response()->json(['success' => true, 'message' => 'Surat berhasil didapatkan berdasarkan id user ' . $userId, 'data' => $surat]);
            } else {
                return response()->json(['success' => true, 'message' => 'Tidak ada surat yang ditemukan berdasarkan id user ' . $userId, 'data' => []]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function getSyaratBySuratJenis($suratJenisId)
    {
        try {
            if (auth()->user()->role->nama !== 'Pemohon') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $syarat = SuratSyarat::with('suratJenis')->where('surat_jenis_id', $suratJenisId)->get();

            return response()->json(['success' => true, 'data' => $syarat]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function uploadDokumenBySuratSyaratId(Request $request, $suratId, $suratJenisId, $suratSyaratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Pemohon') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'surat_id' => 'nullable|exists:surat,id',
                'surat_syarat_id' => 'nullable|exists:surat_syarat,id',
                'dokumen_upload' => 'required|mimes:pdf,doc,docx',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $surat = Surat::find($suratId);
            $suratJenis = SuratJenis::find($suratJenisId);
            $suratSyarat = SuratSyarat::find($suratSyaratId);

            // cek
            if (!$surat || !$suratJenis || !$suratSyarat) {
                return response()->json(['message' => 'Surat, surat jenis, atau surat syarat tidak ditemukan'], 404);
            }

            $dokumenPath = $request->file('dokumen_upload')->storeAs("uploads/documents/surat-dokumen/dokumen-upload/{$suratJenis->nama}/{$suratSyarat->nama}", $request->file('dokumen_upload')->getClientOriginalName());

            $suratDokumen = SuratDokumen::create([
                'surat_id' => $suratId,
                'surat_syarat_id' => $suratSyaratId,
                'dokumen_upload' => $dokumenPath,
            ]);

            $suratDokumen->load(['surat', 'suratSyarat.suratJenis']);

            return response()->json(['message' => 'Surat dokumen berhasil diunggah', 'data' => $suratDokumen]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function updateDokumenBySuratSyaratId(Request $request, $suratId, $suratJenisId, $suratSyaratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Pemohon') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                'dokumen_upload' => 'nullable|mimes:pdf,doc,docx',
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            $surat = Surat::find($suratId);
            $suratJenis = SuratJenis::find($suratJenisId);
            $suratSyarat = SuratSyarat::find($suratSyaratId);

            if (!$surat || !$suratJenis || !$suratSyarat) {
                return response()->json(['message' => 'Surat, surat jenis, atau surat syarat tidak ditemukan'], 404);
            }

            $suratDokumen = SuratDokumen::where([
                'surat_id' => $suratId,
                'surat_syarat_id' => $suratSyaratId,
            ])->first();

            if ($request->hasFile('dokumen_upload')) {
                if ($suratDokumen->dokumen_upload) {
                    // Storage::delete($suratDokumen->dokumen_upload);
                    Storage::delete("uploads/documents/surat-dokumen/dokumen-upload/{$suratJenis->nama}/{$suratSyarat->nama}/" . basename($suratDokumen->dokumen_upload));
                }

                $dokumenUpload = $request->file('dokumen_upload');
                $path = $dokumenUpload->storeAs("uploads/documents/surat-dokumen/dokumen-upload/{$suratJenis->nama}/{$suratSyarat->nama}", $dokumenUpload->getClientOriginalName());
            } else {
                $path = $suratDokumen->dokumen_upload;
            }

            $suratDokumen->update(['dokumen_upload' => $path]);

            $suratDokumen->surat->update(['is_dikembalikan' => 'N', 'alasan_dikembalikan' => null]);

            $suratDokumen->load(['surat', 'suratSyarat.suratJenis']);

            return response()->json(['message' => 'Surat dokumen berhasil diupdate.', 'data' => $suratDokumen]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function suratDiajukan(Request $request, $suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Pemohon') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            // cek validate dokumen
            $cekJumlahSuratSyarat = SuratSyarat::where('surat_jenis_id', $request->surat_jenis_id)->count();
            $cekJumlahSuratDokumen = SuratDokumen::where('surat_id', $suratId)->count();

            if ($cekJumlahSuratDokumen < $cekJumlahSuratSyarat) {
                return response()->json(['message' => 'Surat dokumen belum lengkap, mohon dilengkapi terlebih dahulu'], 404);
            }

            if ($surat->status === 'Pengisian Dokumen') {
                $surat->status = 'Verifikasi Operator';
                $surat->save();

                // PushNotificationController::sendMessage(
                //   Auth::user()->id,
                //   'Permohonan surat telah diajukan',
                //   'Sekarang, kami sedang memproses tahap validasi operator. Harap tunggu pemberitahuan selanjutnya!'
                // );

                Notifikasi::create([
                    'user_id' => Auth::user()->id,
                    'judul' => 'Permohonan surat telah diajukan',
                    'deskripsi' => 'Sekarang, kami sedang memproses tahap validasi operator. Harap tunggu pemberitahuan selanjutnya!',
                    'is_seen' => 'N'
                ]);

                return response()->json(['success' => true, 'message' => 'Permohonan surat telah diajukan']);
            } else {
                return response()->json(['success' => false, 'message' => 'Permohonan surat telah diajukan atau tidak ditemukan']);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // role operator
    public function terimaVerifikasiOperator($suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Operator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Operator') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Verifikasi Verifikator';
            $surat->is_terlambat = 'N';
            $surat->save();

            // PushNotificationController::sendMessage(
            //   $surat->user_id,
            //   'Selamat surat berhasil divalidasi oleh operator',
            //   'Sekarang, kami sedang memproses tahap verifikasi verifikator. Harap tunggu pemberitahuan selanjutnya!'
            // );

            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Selamat surat berhasil divalidasi oleh operator',
                'deskripsi' => 'Sekarang, kami sedang memproses tahap verifikasi verifikator. Harap tunggu pemberitahuan selanjutnya!',
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Selamat surat berhasil divalidasi oleh operator']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolakVerifikasiOperator(Request $request, $suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Operator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Operator') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Pengisian Dokumen';
            $surat->is_dikembalikan = 'Y';
            $surat->is_terlambat = 'N';
            $surat->alasan_dikembalikan = $request->alasan_dikembalikan;
            $surat->save();

            // PushNotificationController::sendMessage(
            //   $surat->user_id,
            //   'Mohon maaf surat anda dikembalikan',
            //   'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' belum dapat diterima pada tahap ini. Harap lakukan pembaruan sesuai dengan yang kami tuliskan alasan dikembalikan pada detail surat. Kami mengapresiasi kerjasama Anda dalam mengatasi hal ini'
            // );

            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Mohon maaf surat anda dikembalikan',
                'deskripsi' => 'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' belum dapat diterima pada tahap ini. Harap lakukan pembaruan sesuai dengan yang kami tuliskan alasan dikembalikan pada detail surat. Kami mengapresiasi kerjasama Anda dalam mengatasi hal ini',
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Mohon maaf surat anda dikembalikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolakVerifikasiOperatorNew(Request $request, $suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Operator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Operator') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Ditolak';
            $surat->is_dikembalikan = 'N';
            $surat->is_terlambat = 'N';
            $surat->alasan_ditolak = $request->alasan_ditolak;
            $surat->save();

            $alasanDitolak = $request->alasan_ditolak;

            // PushNotificationController::sendMessage(
            //   $surat->user_id,
            //   'Mohon maaf surat anda dikembalikan',
            //   'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' belum dapat diterima pada tahap ini. Harap lakukan pembaruan sesuai dengan yang kami tuliskan alasan dikembalikan pada detail surat. Kami mengapresiasi kerjasama Anda dalam mengatasi hal ini'
            // );

            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Mohon maaf surat anda ditolak',
                'deskripsi' => 'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' telah ditolak dengan alasan: ' . $alasanDitolak,
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Mohon maaf surat anda dikembalikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // role verifikator
    public function terimaVerifikasiVerifikator($suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Verifikator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Verifikator') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Penjadwalan Survey';
            $surat->is_terlambat = 'N';
            $surat->save();

            // PushNotificationController::sendMessage(
            //   $surat->user_id,
            //   'Selamat surat berhasil diverifikasi oleh verifikator',
            //   'Sekarang, kami sedang memproses tahap penjadwalan survey. Harap tunggu pemberitahuan selanjutnya!'
            // );

            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Selamat surat berhasil diverifikasi oleh verifikator',
                'deskripsi' => 'Sekarang, kami sedang memproses tahap penjadwalan survey. Harap tunggu pemberitahuan selanjutnya!',
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Selamat surat berhasil diverifikasi oleh verifikator']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolakVerifikasiVerifikator(Request $request, $suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Verifikator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Verifikator') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Pengisian Dokumen';
            $surat->is_dikembalikan = 'Y';
            $surat->is_terlambat = 'N';
            $surat->alasan_dikembalikan = $request->alasan_dikembalikan;
            $surat->save();

            // PushNotificationController::sendMessage(
            //   $surat->user_id,
            //   'Mohon maaf surat anda dikembalikan',
            //   'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' belum dapat diterima pada tahap ini. Harap lakukan pembaruan sesuai dengan yang kami tuliskan alasan dikembalikan pada detail surat. Kami mengapresiasi kerjasama Anda dalam mengatasi hal ini'
            // );

            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Mohon maaf surat anda dikembalikan',
                'deskripsi' => 'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' belum dapat diterima pada tahap ini. Harap lakukan pembaruan sesuai dengan yang kami tuliskan alasan dikembalikan pada detail surat. Kami mengapresiasi kerjasama Anda dalam mengatasi hal ini',
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Mohon maaf surat anda dikembalikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolakVerifikasiVerifikatorNew(Request $request, $suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Verifikator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Verifikator') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Ditolak';
            $surat->is_dikembalikan = 'N';
            $surat->is_terlambat = 'N';
            $surat->alasan_ditolak = $request->alasan_ditolak;
            $surat->save();

            $alasanDitolak = $request->alasan_ditolak;

            // PushNotificationController::sendMessage(
            //   $surat->user_id,
            //   'Mohon maaf surat anda dikembalikan',
            //   'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' belum dapat diterima pada tahap ini. Harap lakukan pembaruan sesuai dengan yang kami tuliskan alasan dikembalikan pada detail surat. Kami mengapresiasi kerjasama Anda dalam mengatasi hal ini'
            // );

            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Mohon maaf surat anda ditolak',
                'deskripsi' => 'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' telah ditolak dengan alasan: ' . $alasanDitolak,
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Mohon maaf surat anda dikembalikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function setJadwalSurvey(Request $request, $suratId)
    {
        try {
            $validator = Validator::make($request->all(), [
                "surat_id" => "nullable|exists:surat,id",
                "user_id" => "required|exists:user,id",
                "nama_survey" => "required|string",
                "deskripsi_survey" => "required|string",
                "status" => "nullable|in:Belum Disurvey,Sudah Disurvey,Survey Disetujui,Survey Ditolak",
                "jadwal_survey" => "nullable|date",
                "tenggat_survey" => "nullable|date",
                "dokumen_surat_tugas" => "nullable|file|mimes:pdf,doc,docx",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            };

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Penjadwalan Survey') {
                return response()->json(['message' => 'Surat tidak dapat dijadwalkan untuk survey saat ini'], 400);
            }

            $dokumenPath = $request->file('dokumen_surat_tugas')->storeAs("uploads/documents/survey/dokumen-surat-tugas", $request->file('dokumen_surat_tugas')->getClientOriginalName());

            // Pastikan user yang membuat jadwal adalah verifikator
            if (auth()->user()->role->nama === 'Verifikator') {
                $surat->update(['status' => 'Verifikasi Hasil Survey', 'jadwal_survey' => $request->jadwal_survey]);

                $survey = Survey::create([
                    'surat_id' => $suratId,
                    'user_id' => $request->user_id,
                    'nama_survey' => $request->nama_survey,
                    'deskripsi_survey' => $request->deskripsi_survey,
                    'status' => 'Belum Disurvey',
                    'jadwal_survey' => $request->jadwal_survey,
                    'tenggat_survey' => $request->tenggat_survey,
                    "dokumen_surat_tugas" => $dokumenPath ?? null,
                ]);

                // buat pemohon
                Notifikasi::create([
                    'user_id' => $surat->user_id,
                    'judul' => 'Selamat jadwal survey telah ditentukan',
                    'deskripsi' => 'Kami memberitahukan bahwa jadwal survey untuk surat dengan nomor ' . $suratId . ' berhasil ditentukan. Mohon segera cek jadwal Anda dan pastikan kesiapan untuk melaksanakan survey',
                    'is_seen' => 'N'
                ]);

                // buat surveyor
                Notifikasi::create([
                    'user_id' => $survey->user_id,
                    'judul' => 'Selamat jadwal survey telah ditentukan',
                    'deskripsi' => 'Kami memberitahukan bahwa jadwal survey untuk surat dengan nomor ' . $suratId . ' berhasil ditentukan. Mohon segera cek jadwal Anda dan pastikan kesiapan untuk melaksanakan survey',
                    'is_seen' => 'N'
                ]);

                return response()->json(['success' => true, 'message' => 'Jadwal survey berhasil ditetapkan']);
            } else {
                return response()->json(['success' => false, 'message' => 'Anda tidak memiliki izin untuk menetapkan jadwal survey'], 403);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // role surveyor
    public function terimaHasilSurvey(Request $request, $surveyId)
    {
        try {
            if (auth()->user()->role->nama !== 'Surveyor') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $validator = Validator::make($request->all(), [
                "status" => "nullable|in:Belum Disurvey,Sudah Disurvey,Survey Disetujui,Survey Ditolak",
//                "jadwal_survey" => "nullable|date",
                "alamat_survey" => "nullable|string|max:255",
                "foto_survey" => "nullable|mimes:jpeg,png,jpg,gif,svg",
                "longitude" => "nullable|numeric",
                "latitude" => "nullable|numeric",
                "dokumen_survey" => "nullable|mimes:pdf,doc,docx",
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            };

            $survey = Survey::findOrFail($surveyId);

            // foto
            if ($request->hasFile('foto_survey')) {
                if ($survey->foto_survey) {
                    Storage::delete("uploads/documents/survey/foto-survey/" . basename($survey->foto_survey));
                }

                $fotoUpload = $request->file('foto_survey');
                $pathFoto = $fotoUpload->storeAs("uploads/documents/survey/foto-survey", $fotoUpload->getClientOriginalName());
            } else {
                $pathFoto = $survey->foto_survey;
            }

            // dokumen
            if ($request->hasFile('dokumen_survey')) {
                if ($survey->dokumen_survey) {
                    Storage::delete("uploads/documents/survey/dokumen-survey/" . basename($survey->dokumen_survey));
                }

                $dokumenUpload = $request->file('dokumen_survey');
                $path = $dokumenUpload->storeAs("uploads/documents/survey/dokumen-survey", $dokumenUpload->getClientOriginalName());
            } else {
                $path = $survey->dokumen_survey;
            }

            $dataToUpdate = $request->only(['status', 'jadwal_survey', 'alamat_survey', 'foto_survey', 'longitude', 'latitude', 'dokumen_survey']);
            $dataToUpdate['status'] = "Sudah Disurvey";
            $dataToUpdate['foto_survey'] = $pathFoto;
            $dataToUpdate['dokumen_survey'] = $path;
//            $dataToUpdate['jadwal_survey'] = Carbon::parse($request->jadwal_survey)->toDateTimeString();

            $survey->update($dataToUpdate);

            $surat = $survey->surat;
            $surat->update(['status' => 'Verifikasi Hasil Survey']);

            // buat pemohon
            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Selamat survey anda telah diselesaikan',
                'deskripsi' => 'Kami memberitahukan bahwa survey anda untuk surat dengan nomor ' . $survey->id . ' berhasil diselesaikan. Mohon segera cek jadwal Anda dan pastikan kesiapan untuk melaksanakan survey',
                'is_seen' => 'N'
            ]);

            // buat verifikator
            Notifikasi::create([
                'user_id' => $survey->user_id,
                'judul' => 'Selamat survey anda telah diselesaikan',
                'deskripsi' => 'Kami memberitahukan bahwa survey anda untuk surat dengan nomor ' . $survey->id . ' berhasil diselesaikan. Mohon segera cek jadwal Anda dan pastikan kesiapan untuk melaksanakan survey',
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'survey berhasil diselesaikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    // role verifikator
    public function terimaVerifikasiSurvey($suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Verifikator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Hasil Survey') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Selesai';
            $surat->save();

            // buat pemohon
            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Selamat Surat anda telah diterima dan selesai',
                'deskripsi' => 'Kami memberitahukan bahwa surat anda untuk surat dengan nomor ' . $surat->id . ' berhasil diterima dan berstatus selesai',
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'survey berhasil diselesaikan']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }

    public function tolakVerifikasiSurvey(Request $request, $suratId)
    {
        try {
            if (auth()->user()->role->nama !== 'Verifikator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $surat = Surat::findOrFail($suratId);

            if ($surat->status !== 'Verifikasi Hasil Survey') {
                return response()->json(['message' => 'Surat belum bisa di verifikasi karena belum memenuhi tahapan syarat'], 403);
            }

            $surat->status = 'Ditolak';
            $surat->alasan_ditolak = $request->alasan_ditolak;
            $surat->save();

            $alasanDitolak = $request->input('alasan_ditolak');

            // buat pemohon
            Notifikasi::create([
                'user_id' => $surat->user_id,
                'judul' => 'Mohon maaf surat anda dikembalikan',
                'deskripsi' => 'Surat yang Anda ajukan dengan nomer ' . $surat->id . ' telah ditolak dengan alasan: ' . $alasanDitolak,
                'is_seen' => 'N'
            ]);

            return response()->json(['success' => true, 'message' => 'Mohon maaf surat anda ditolak']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
