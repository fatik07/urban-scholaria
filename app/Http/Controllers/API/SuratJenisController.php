<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SuratJenis;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuratJenisController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $suratJenis = SuratJenis::all();
      return response()->json(['success' => true, 'message' => 'Semua surat jenis berhasil didapatkan', 'data' => $suratJenis]);
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
      $validator = Validator::make($request->all(), [
        "nama" => "required|string|max:255",
        "gambar_alur_permohonan" => "nullable|mimes:png,jpg,jpeg",
        "gambar_service_level_aggreement" => "nullable|mimes:png,jpg,jpeg"
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      };

      $gambarAlurPermohonan = $request->file('gambar_alur_permohonan')->storeAs("public/documents/surat-jenis/gambar-alur-permohonan", $request->file('gambar_alur_permohonan')->getClientOriginalName());

      $gambarServiceLevelAgreement = $request->file('gambar_service_level_aggreement')->storeAs("public/documents/surat-jenis/gambar-service-level-aggreement", $request->file('gambar_service_level_aggreement')->getClientOriginalName());

      $suratJenis = SuratJenis::create([
        'nama' => $request->nama,
        'gambar_alur_permohonan' => $gambarAlurPermohonan,
        'gambar_service_level_aggreement' => $gambarServiceLevelAgreement
      ]);

      return response()->json([
        "success" => true,
        "data" => $suratJenis,
        "message" => "Surat jenis berhasil dibuat",
      ]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  /**
   * Display the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */
  public function show($id)
  {
    try {
      $suratJenis = SuratJenis::find($id);

      if ($suratJenis) {
        return response()->json(['success' => true, 'message' => 'Surat jenis berhasil didapatkan', 'data' => $suratJenis]);
      }
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
      $suratJenis = SuratJenis::find($id);

      if (!$suratJenis) {
        return response()->json(['message' => 'Surat jenis tidak ditemukan'], 404);
      }

      $validator = Validator::make($request->all(), [
        "nama" => "nullable|string|max:255",
        "gambar_alur_permohonan" => "nullable|mimes:png,jpg,jpeg",
        "gambar_service_level_aggreement" => "nullable|mimes:png,jpg,jpeg"
      ]);

      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
      }

      if ($request->hasFile('gambar_alur_permohonan')) {
        if ($suratJenis->gambar_alur_permohonan) {
          Storage::delete($suratJenis->gambar_alur_permohonan);
        }

        $gambarAlurPermohonanBaru = $request->file('gambar_alur_permohonan')->storeAs("public/documents/surat-jenis/gambar-alur-permohonan", $request->file('gambar_alur_permohonan')->getClientOriginalName());

        $suratJenis->gambar_alur_permohonan = $gambarAlurPermohonanBaru;
      }

      if ($request->hasFile('gambar_service_level_aggreement')) {
        if ($suratJenis->gambar_service_level_aggreement) {
          Storage::delete($suratJenis->gambar_service_level_aggreement);
        }

        $gambarServiceLevelAggreementBaru = $request->file('gambar_service_level_aggreement')->storeAs("public/documents/surat-jenis/gambar-service-level-aggreement", $request->file('gambar_service_level_aggreement')->getClientOriginalName());

        $suratJenis->gambar_service_level_aggreement = $gambarServiceLevelAggreementBaru;
      }

      $dataToUpdate = $request->only(['nama', 'gambar_alur_permohonan', 'gambar_service_level_aggreement']);

      $suratJenis->update($dataToUpdate);

      return response()->json(['success' => true, 'message' => 'Surat jenis berhasil diupdate', 'data' => $suratJenis]);
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
      $suratJenis = SuratJenis::findOrFail($id);

      $gambarAlurPermohonan = $suratJenis->gambar_alur_permohonan;
      $gambarServiceLevelAggreement = $suratJenis->gambar_service_level_aggreement;

      if (!$suratJenis) {
        return response()->json(['message' => 'Surat jenis tidak ditemukan'], 404);
      }

      if (Storage::exists($gambarAlurPermohonan)) {
        Storage::delete($gambarAlurPermohonan);
      }

      if (Storage::exists($gambarServiceLevelAggreement)) {
        Storage::delete($gambarServiceLevelAggreement);
      }

      $suratJenis->delete();

      return response()->json(['success' => true, 'message' => 'Surat jenis berhasil dihapus']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
