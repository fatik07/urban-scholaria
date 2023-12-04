<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SuratDokumen;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class SuratDokumenController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index()
  {
    try {
      $suratDokumen = SuratDokumen::all();
      return response()->json(['success' => true, 'message' => 'Semua surat dokumen berhasil didapatkan', 'data' => $suratDokumen]);
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
        "surat_id" => "required|exists:surat,id",
        "surat_syarat_id" => "required|exists:surat_syarat,id",
        "dokumen_upload" => "required|mimes:pdf,doc,docx,png,jpg,jpeg"
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      };

      if ($request->hasFile('dokumen_upload')) {
        $dokumenUpload = $request->file('dokumen_upload');
        $dokumenUploadPath = $dokumenUpload->storeAs("uploads/document/surat-dokumen/dokumen-upload", $dokumenUpload->getClientOriginalName());
      } else {
        $dokumenUploadPath = null;
      }

      $suratDokumen = SuratDokumen::create([
        "surat_id" => $request->surat_id,
        "surat_syarat_id" => $request->surat_syarat_id,
        "dokumen_upload" => $dokumenUploadPath
      ]);

      return response()->json([
        "success" => true,
        "data" => $suratDokumen,
        "message" => "Surat dokumen berhasil dibuat",
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
      $suratDokumen = SuratDokumen::find($id);

      if ($suratDokumen) {
        return response()->json(['success' => true, 'message' => 'Surat dokumen berhasil didapatkan', 'data' => $suratDokumen]);
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
      $suratDokumen = SuratDokumen::find($id);

      if (!$suratDokumen) {
        return response()->json(['message' => 'Surat dokumen tidak ditemukan'], 404);
      }

      $validator = Validator::make($request->all(), [
        "surat_id" => "nullable|exists:surat,id",
        "surat_syarat_id" => "nullable|exists:surat_syarat,id",
        "dokumen_upload" => "nullable|mimes:pdf,doc,docx,png,jpg,jpeg"
      ]);

      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
      }

      if ($request->hasFile('dokumen_upload')) {
        if ($suratDokumen->dokumen_upload) {
          Storage::delete("public/documents/surat-dokumen/dokumen-upload/" . basename($suratDokumen->dokumen_upload));
        }

        $dokumenUpload = $request->file('dokumen_upload');
        $path = $dokumenUpload->storeAs("public/documents/surat-dokumen/dokumen-upload", $dokumenUpload->getClientOriginalName());

        $suratDokumen->dokumen_upload = $path;
      }

      if ($request->filled('surat_id')) {
        $suratDokumen->surat_id = $request->surat_id;
      }

      if ($request->filled('surat_syarat_id')) {
        $suratDokumen->surat_syarat_id = $request->surat_syarat_id;
      }

      $suratDokumen->save();

      return response()->json(['success' => true, 'message' => 'Surat dokumen berhasil diupdate', 'data' => $suratDokumen]);
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
      $suratDokumen = SuratDokumen::findOrFail($id);

      if (!$suratDokumen) {
        return response()->json(['message' => 'Surat dokumen tidak ditemukan'], 404);
      }

      $suratDokumen->delete();

      return response()->json(['success' => true, 'message' => 'Surat dokumen berhasil dihapus']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
