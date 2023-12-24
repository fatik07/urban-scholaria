<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\Survey;
use App\Models\User;
use Illuminate\Http\Request;

class SurveyorController extends Controller
{
    public function index(Request $request)
    {
        try {
            if (auth()->user()->role->nama !== 'Surveyor' && auth()->user()->role->nama !== 'Verifikator') {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }

            $userId = $request->query('user_id');
            $role = $request->query('role');
            $suratId = $request->query('surat_id');

            if ($userId) {
                $user = User::find($userId);

                if (!$user) {
                    return response()->json(['success' => false, 'message' => "User dengan id " . $userId . " tidak ditemukan"]);
                }

                $surveys = Survey::with('surat.user', 'surat.suratJenis')->where('user_id', $userId)->get();
                return response()->json(['success' => true, 'message' => "Survey berhasil didapatkan dengan user id " . $userId, 'data' => $surveys]);
            } elseif ($role) {
                $expectedRole = 'Surveyor';

                if ($role === $expectedRole) {
                    $surveyorRole = Role::where('nama', $expectedRole)->first();

                    if ($surveyorRole) {
                        $surveyors = User::where('role_id', $surveyorRole->id)->get();

                        return response()->json(['success' => true, 'message' => 'Surveyor berhasil didapatkan dengan role Surveyor', 'data' => $surveyors]);
                    } else {
                        return response()->json(['success' => false, 'message' => 'Role Surveyor tidak ditemukan']);
                    }
                } else {
                    return response()->json(['success' => false, 'message' => 'Parameter role harus bernilai Surveyor']);
                }
            } elseif ($suratId) {
                $surveys = Survey::with('surat.user', 'surat.suratJenis')->where('surat_id', $suratId)->get();

                if (!$surveys) {
                    return response()->json(['success' => false, 'message' => "Survey dengan surat id " . $suratId . " tidak ditemukan"], 404);
                }

                return response()->json(['success' => true, 'message' => 'Survey berhasil didapatkan dengan surat id ' . $suratId, 'data' => $surveys]);
            } else {
                $surveys = Survey::with('surat.user', 'surat.suratJenis')->get();

                return response()->json(['success' => true, 'message' => 'Semua survey berhasil didapatkan', 'data' => $surveys]);
            }
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()]);
        }
    }
}
