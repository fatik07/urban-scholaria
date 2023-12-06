<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\SuratSyarat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class SuratSyaratController extends Controller
{
  /**
   * Display a listing of the resource.
   *
   * @return \Illuminate\Http\Response
   */
  public function index(Request $request)
  {
    try {
      $surat_jenis = $request->query('surat_jenis_id');

      $query = SuratSyarat::query();
      $message = "Surat syarat berhasil didapatkan";

      if ($surat_jenis) {
        $query->where('surat_jenis_id', $surat_jenis);
        $message .= " dengan surat jenis id " . $surat_jenis;
      }

      $suratSyarat = $query->get();

      if ($suratSyarat->isEmpty()) {
        return response()->json(['success' => false, 'message' => 'Maaf, tidak ada surat syarat yang sesuai']);
      }

      return response()->json(['success' => true, 'message' => $message, 'data' => $suratSyarat]);
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
        "surat_jenis_id" => "required|exists:surat_jenis,id",
        "nama" => "required|string|max:255"
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      };

      $suratSyarat = SuratSyarat::create([
        'surat_jenis_id' => $request->surat_jenis_id,
        'nama' => $request->nama
      ]);

      return response()->json([
        "success" => true,
        "data" => $suratSyarat,
        "message" => "Surat syarat berhasil dibuat",
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
      $suratSyarat = SuratSyarat::with('suratJenis')->find($id);

      if ($suratSyarat) {
        return response()->json(['success' => true, 'message' => 'Surat syarat berhasil didapatkan', 'data' => $suratSyarat]);
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
      $suratSyarat = SuratSyarat::with('suratJenis')->find($id);

      if (!$suratSyarat) {
        return response()->json(['message' => 'Surat syarat tidak ditemukan'], 404);
      }

      $validator = Validator::make($request->all(), [
        "surat_jenis_id" => "nullable|exists:surat_jenis,surat_jenis_id",
        "nama" => "nullable|string|max:255"
      ]);

      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
      }

      $dataToUpdate = $request->only(['nama', 'surat_jenis_id']);

      $suratSyarat->update($dataToUpdate);

      return response()->json(['success' => true, 'message' => 'Surat syarat berhasil diupdate', 'data' => $suratSyarat]);
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
      $suratSyarat = SuratSyarat::findOrFail($id);

      if (!$suratSyarat) {
        return response()->json(['message' => 'Surat syarat tidak ditemukan'], 404);
      }

      $suratSyarat->delete();

      return response()->json(['success' => true, 'message' => 'Surat syarat berhasil dihapus']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
