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
                "deskripsi" => "required|string",
                "gambar_alur_permohonan" => "nullable|mimes:png,jpg,jpeg",
                "gambar_service_level_aggreement" => "nullable|mimes:png,jpg,jpeg"
            ]);

            if ($validator->fails()) {
                return response()->json($validator->errors());
            };

            //gambar_alur_permohonan
            if ($request->hasFile('gambar_alur_permohonan')) {
                $gambarAlurPermohonan = $request->file('gambar_alur_permohonan');
                $gambarAlurPermohonanPath = $gambarAlurPermohonan->storeAs("uploads/documents/surat-jenis/gambar-alur-permohonan", $gambarAlurPermohonan->getClientOriginalName());
            } else {
                $gambarAlurPermohonanPath = null;
            }

            //gambar_service_level_aggreement
            if ($request->hasFile('gambar_service_level_aggreement')) {
                $gambarServiceLevelAgreement = $request->file('gambar_service_level_aggreement');
                $gambarServiceLevelAgreementPath = $gambarServiceLevelAgreement->storeAs("uploads/documents/surat-jenis/gambar-service-level-aggreement", $gambarServiceLevelAgreement->getClientOriginalName());
            } else {
                $gambarServiceLevelAgreementPath = null;
            }

            $suratJenis = SuratJenis::create([
                'nama' => $request->nama,
                'deskripsi' => $request->deskripsi,
                'gambar_alur_permohonan' => $gambarAlurPermohonanPath,
                'gambar_service_level_aggreement' => $gambarServiceLevelAgreementPath
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
            $suratJenis = SuratJenis::with('suratSyarats')->find($id);

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
                "deskripsi" => "nullable|string",
                "gambar_alur_permohonan" => "nullable|mimes:png,jpg,jpeg",
                "gambar_service_level_aggreement" => "nullable|mimes:png,jpg,jpeg"
            ]);

            if ($validator->fails()) {
                return response()->json(['errors' => $validator->errors()], 400);
            }

            if ($request->hasFile('gambar_alur_permohonan') && $suratJenis->gambar_alur_permohonan) {
                Storage::delete($suratJenis->gambar_alur_permohonan);
            }

            if ($request->hasFile('gambar_service_level_aggreement') && $suratJenis->gambar_service_level_aggreement) {
                Storage::delete($suratJenis->gambar_service_level_aggreement);
            }

            // Proses upload dan update gambar jika ada
            if ($request->hasFile('gambar_alur_permohonan')) {
                $gambarAlurPermohonanPath = $request->file('gambar_alur_permohonan')->storeAs(
                    "uploads/documents/surat-jenis/gambar-alur-permohonan",
                    $request->file('gambar_alur_permohonan')->getClientOriginalName()
                );
                $suratJenis->gambar_alur_permohonan = $gambarAlurPermohonanPath;
            }

            if ($request->hasFile('gambar_service_level_aggreement')) {
                $gambarServiceLevelAggreementPath = $request->file('gambar_service_level_aggreement')->storeAs(
                    "uploads/documents/surat-jenis/gambar-service-level-aggreement",
                    $request->file('gambar_service_level_aggreement')->getClientOriginalName()
                );
                $suratJenis->gambar_service_level_aggreement = $gambarServiceLevelAggreementPath;
            }

            // $suratJenis->update($dataToUpdate);
            $suratJenis->update($request->only(["nama", "deskripsi"]));

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

            if (!is_null($gambarAlurPermohonan) && Storage::exists($gambarAlurPermohonan)) {
                Storage::delete($gambarAlurPermohonan);
            }

            if (!is_null($gambarServiceLevelAggreement) && Storage::exists($gambarServiceLevelAggreement)) {
                Storage::delete($gambarServiceLevelAggreement);
            }

            $suratJenis->delete();

            return response()->json(['success' => true, 'message' => 'Surat jenis berhasil dihapus']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
