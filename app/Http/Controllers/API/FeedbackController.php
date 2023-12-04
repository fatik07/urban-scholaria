<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Surat;
use App\Models\Ulasan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
  public function index()
  {
    try {
      $feedbacks = Ulasan::with('surat')->get();
      return response()->json(['success' => true, 'message' => 'Data feedback berhasil diambil', 'data' => $feedbacks]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function create(Request $request)
  {
    try {
      if (auth()->user()->role->nama !== 'Pemohon') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $validator = Validator::make($request->all(), [
        "surat_id" => "required|exists:surat,id",
        "isi" => "required|string|max:255",
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      };

      $suratId = $request->surat_id;
      $surat = Surat::findOrFail($suratId);

      if ($surat->status !== 'Selesai') {
        return response()->json(['message' => 'Surat belum selesai'], 400);
      }

      Ulasan::create([
        'surat_id' => $suratId,
        'isi' => $request->isi,
      ]);

      return response()->json(['success' => true, 'message' => 'Feedback berhasil disimpan']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
