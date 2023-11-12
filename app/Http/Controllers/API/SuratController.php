<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Surat;
use App\Models\SuratDokumen;
use App\Models\SuratJenis;
use App\Models\SuratSyarat;
use Auth;
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
      if (auth()->user()->role->nama !== 'Pemohon') {
        $status = $request->query('status');

        if ($status) {
          $query = Surat::with('suratDokumen');

          if ($status) {
            $query->where('status', $status);
          }

          $surat = $query->get();

          return response()->json(['success' => true, 'message' => 'Surat berhasil didapatkan dengan status verifikasi operator', 'data' => $surat]);
        } else {
          $surat = Surat::with('suratDokumen')->get();

          return response()->json(['success' => true, 'message' => 'Semua surat berhasil didapatkan', 'data' => $surat]);
        }
      } else {
        $status = $request->query('status');

        if ($status) {
          return response()->json(['message' => 'Akses ditolak'], 403);
        }

        $surat = Surat::with('suratDokumen')->get();

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
        "status" => "nullable|in:Pengisian Dokumen,Verifikasi Operator,Verifikasi Verifikator,Penjadwalan Survey,Verifikasi Hasil Survey,Verifikasi Kepala Dinas,Selesai,Ditolak",
        "is_ulasan" => "nullable|in:Y,N",
        "kategori" => "nullable|in:TK,SD,SMP,SMA,SMK",
        "alamat_lokasi" => "nullable|string|max:255",
        "longitude" => "nullable|string|max:255",
        "latitude" => "nullable|string|max:255",
        "jadwal_survey" => "nullable|date",
        "nomor_penerbitan" => "nullable|string|max:255",
        "is_dikembalikan" => "nullable|in:Y,N",
        "alasan_dikembalikan" => "nullable|string|max:255",
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      };

      $surat = Surat::create([
        "user_id" => Auth::user()->id,
        "status" => 'Pengisian Dokumen',
        "is_ulasan" => 'N',
        "kategori" => $request->kategori,
        "alamat_lokasi" => $request->alamat_lokasi,
        "longitude" => $request->longitude,
        "latitude" => $request->latitude,
        "jadwal_survey" => null,
        "nomor_penerbitan" => null,
        "is_dikembalikan" => 'N',
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
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function update(Request $request, $id)
  {
    try {
      $surat = Surat::find($id);

      if (!$surat) {
        return response()->json(['message' => 'Surat tidak ditemukan'], 404);
      }

      if (!$surat->is_dikembalikan === 'Y') {
        $validator = Validator::make($request->all(), [
          "kategori" => "nullable|in:TK,SD,SMP,SMA,SMK",
          "alamat_lokasi" => "nullable|string|max:255",
          "longitude" => "nullable|string|max:255",
          "latitude" => "nullable|string|max:255",
          "is_dikembalikan" => "nullable|in:Y,N",
          "alasan_dikembalikan" => "nullable|string|max:255",
          'dokumen_upload' => 'nullable|mimes:pdf,doc,docx',
        ]);

        if ($validator->fails()) {
          return response()->json(['errors' => $validator->errors()], 400);
        }

        $kategori = optional($request->input('kategori'))->get();
        $is_dikembalikan = 'N';
        $alasan_dikembalikan = null;

        $dataToUpdate = $request->only(['kategori', 'alamat_lokasi', 'longitude', 'latitude', 'is_dikembalikan', 'alasan_dikembalikan']);
        $dataToUpdate['kategori'] = $kategori;
        $dataToUpdate['is_dikembalikan'] = $is_dikembalikan;
        $dataToUpdate['alasan_dikembalikan'] = $alasan_dikembalikan;

        $surat->update($dataToUpdate);

        return response()->json(['success' => true, 'message' => 'Surat berhasil diupdate', 'data' => $surat]);
      } else {
        return response()->json(['success' => false, 'message' => 'Tidak bisa update surat, karena is_dikembalikan masih false']);
      }
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
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
      if (auth()->user()->role->nama !== 'Pemohon') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $surat = Surat::where('user_id', $userId)->get();

      if ($surat) {
        return response()->json(['success' => true, 'message' => 'Surat berhasil didapatkan berdasarkan pemohon', 'data' => $surat]);
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

      $syarat = SuratSyarat::where('surat_jenis_id', $suratJenisId)->get();

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

      if (!$surat) {
        return response()->json(['message' => 'Surat tidak ditemukan'], 404);
      }

      $dokumenPath = $request->file('dokumen_upload')->storeAs("public/documents/surat-dokumen/dokumen-upload/{$suratJenis->nama}/{$suratSyarat->nama}", $request->file('dokumen_upload')->getClientOriginalName());

      $suratDokumen = SuratDokumen::create([
        'surat_id' => $suratId,
        'surat_syarat_id' => $suratSyaratId,
        'dokumen_upload' => $dokumenPath,
      ]);

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
          Storage::delete("public/documents/surat-dokumen/dokumen-upload/{$suratJenis->nama}/{$suratSyarat->nama}/" . basename($suratDokumen->dokumen_upload));
        }

        $dokumenUpload = $request->file('dokumen_upload');
        $path = $dokumenUpload->storeAs("public/documents/surat-dokumen/dokumen-upload/{$suratJenis->nama}/{$suratSyarat->nama}", $dokumenUpload->getClientOriginalName());
      } else {
        $path = $suratDokumen->dokumen_upload;
      }

      $suratDokumen->update(['dokumen_upload' => $path]);

      $suratDokumen->surat->update(['is_dikembalikan' => 'N', 'alasan_dikembalikan' => null]);

      return response()->json(['message' => 'Surat dokumen berhasil diupdate.', 'data' => $suratDokumen]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function suratSelesai($suratId)
  {
    try {
      if (auth()->user()->role->nama !== 'Pemohon') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $surat = Surat::findOrFail($suratId);

      if ($surat->status === 'Pengisian Dokumen') {
        $surat->status = 'Verifikasi Operator';
        $surat->save();

        Notifikasi::create([
          'user_id' => Auth::user()->id,
          'judul' => 'Permohonan surat telah dibuat',
          'deskripsi' => 'Segera untuk diverifikasi sebelum tanggal verifikasi habis',
          'is_seen' => 'N'
        ]);

        return response()->json(['success' => true, 'message' => 'Surat berhasil diselesaikan']);
      } else {
        return response()->json(['success' => false, 'message' => 'Surat sudah selesai atau tidak ditemukan']);
      }
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  // // role operator
  public function terimaVerifikasiOperator($suratId)
  {
    try {
      if (auth()->user()->role->nama !== 'Operator') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $surat = Surat::findOrFail($suratId);

      $surat->status = 'Verifikasi Verifikator';
      $surat->save();

      Notifikasi::create([
        'user_id' => $surat->user_id,
        'judul' => 'Surat berhasil di validasi oleh operator',
        'deskripsi' => 'Segera untuk cek suratnya',
        'is_seen' => 'N'
      ]);

      return response()->json(['success' => true, 'message' => 'Verifikasi berhasil divalidasi oleh operator']);
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

      $surat->status = 'Pengisian Dokumen';
      $surat->is_dikembalikan = 'Y';
      $surat->alasan_dikembalikan = $request->alasan_dikembalikan;
      $surat->save();

      Notifikasi::create([
        'user_id' => $surat->user_id,
        'judul' => 'Surat gagal di validasi oleh operator',
        'deskripsi' => 'Segera untuk cek suratnya, untuk melihat alasannya',
        'is_seen' => 'N'
      ]);

      return response()->json(['success' => true, 'message' => 'Verifikasi gagal divalidasi oleh operator']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  // // role verifikator
  public function terimaVerifikasiVerifikator($suratId)
  {
    try {
      if (auth()->user()->role->nama !== 'Verifikator') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $surat = Surat::findOrFail($suratId);

      $surat->status = 'Penjadwalan Survey';
      $surat->save();

      Notifikasi::create([
        'user_id' => $surat->user_id,
        'judul' => 'Surat berhasil di validasi oleh verifikator',
        'deskripsi' => 'Segera untuk cek suratnya',
        'is_seen' => 'N'
      ]);

      return response()->json(['success' => true, 'message' => 'Verifikasi berhasil divalidasi oleh verifikator']);
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

      $surat->status = 'Pengisian Dokumen';
      $surat->is_dikembalikan = 'Y';
      $surat->alasan_dikembalikan = $request->alasan_dikembalikan;
      $surat->save();

      Notifikasi::create([
        'user_id' => $surat->user_id,
        'judul' => 'Surat gagal di validasi oleh verifikator',
        'deskripsi' => 'Segera untuk cek suratnya, untuk melihat alasannya',
        'is_seen' => 'N'
      ]);

      return response()->json(['success' => true, 'message' => 'Verifikasi gagal divalidasi oleh verifikator']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
