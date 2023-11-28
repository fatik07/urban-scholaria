<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        "username" => "required|string|max:255",
        "email" => "required|string|max:255|unique:user",
        "password" => "required|string|min:8",
        "nama_lengkap" => "required|string|max:255",
        "foto" => "nullable|mimes:jpeg,png,jpg",
        "jenis_identitas" => "nullable|in:KTP,Paspor",
        "nomor_identitas" => "nullable|string|max:100|unique:user,nomor_identitas",
        "jenis_kelamin" => "nullable|in:Laki-Laki,Perempuan",
        "tempat_lahir" => "nullable|string|max:100",
        "tanggal_lahir" => "nullable|date",
        "provinsi" => "nullable|string|max:100",
        "kabupaten_kota" => "nullable|string|max:100",
        "kecamatan" => "nullable|string|max:100",
        "kelurahan" => "nullable|string|max:100",
        "alamat" => "nullable|string",
        "no_telp" => "nullable|string|max:100|unique:user,no_telp",
        "pekerjaan" => "nullable|string|max:100",
        "is_login" => "nullable|in:Y,N",
        "is_active" => "nullable|in:Y,N",
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      }

      // foto
      if ($request->hasFile('foto_survey')) {
        $fotoUpload = $request->file('foto_survey');
        $pathFoto = $fotoUpload->storeAs("public/foto-profile", $fotoUpload->getClientOriginalName());
      } else {
        $pathFoto = null;
      }

      $user = User::create([
        'role_id' => 9,
        'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'nama_lengkap' => $request->nama_lengkap,
        'foto' => $pathFoto,
        'jenis_identitas' => $request->jenis_identitas,
        'nomor_identitas' => $request->nomor_identitas,
        'jenis_kelamin' => $request->jenis_kelamin,
        'tempat_lahir' => $request->tempat_lahir,
        'tanggal_lahir' => $request->tanggal_lahir,
        'provinsi' => $request->provinsi,
        'kabupaten_kota' => $request->kabupaten_kota,
        'kecamatan' => $request->kecamatan,
        'kelurahan' => $request->kelurahan,
        'alamat' => $request->alamat,
        'no_telp' => $request->no_telp,
        'pekerjaan' => $request->pekerjaan,
        'is_login' => 'N',
        'is_active' => 'N',
      ]);

      $token = $user->createToken("auth_token")->plainTextToken;

      return response()->json([
        "success" => true,
        "data" => $user,
        "message" => "Registrasi berhasil dilakukan",
        "access_token" => $token,
      ]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function login(Request $request)
  {
    try {
      $credentials = $request->only(['email', 'password']);

      if (!Auth::attempt($credentials)) {
        return response()->json([
          "success" => false,
          "message" => "Unauthorized"
        ], 401);
      }

      $user = User::where('email', $request->email)->firstOrFail();

      if ($user->is_active === 'N') {
        return response()->json(['message' => 'Akun Anda belum diaktifkan oleh admin.'], 401);
      }

      $user->is_login = 'Y';
      $user->save();

      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'success' => true,
        'message' => 'Login berhasil dilakukan',
        'access_token' => $token
      ]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function logout(Request $request)
  {
    try {
      $user = $request->user();

      Auth::user()->tokens()->delete();

      $user->is_login = 'N';
      $user->save();

      return response()->json([
        "success" => true,
        "message" => "Logout berhasil dilakukan"
      ]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function getProfile()
  {
    try {
      $user = Auth::user()->load('role');
      return response()->json(['success' => true, 'message' => 'Profile berhasil didapatkan', 'data' => $user]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function updateProfile(Request $request)
  {
    try {
      $user = Auth::user();

      $validator = Validator::make($request->all(), [
        "role_id" => "nullable|exists:role,id",
        'username' => 'string|max:255|nullable',
        'email' => 'string|nullable|email|max:255|unique:user,email,' . $user->id,
        'password' => 'nullable|string|min:8',
        'nama_lengkap' => 'string|max:255|nullable',
        "foto" => "nullable|mimes:jpeg,png,jpg",
        'jenis_identitas' => 'nullable|in:KTP,Paspor',
        'nomor_identitas' => 'nullable|string|max:100|unique:user,nomor_identitas,' . $user->id,
        'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date',
        'provinsi' => 'nullable|string|max:100',
        'kabupaten_kota' => 'nullable|string|max:100',
        'kecamatan' => 'nullable|string|max:100',
        'kelurahan' => 'nullable|string|max:100',
        'alamat' => 'nullable|string',
        'no_telp' => 'nullable|string|max:100|unique:user,no_telp,' . $user->id,
        'pekerjaan' => 'nullable|string|max:100',
        'is_login' => 'nullable|in:Y,N',
        'is_active' => 'nullable|in:Y,N'
      ]);

      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
      }

      // foto
      if ($request->hasFile('foto')) {
        if ($user->foto) {
          Storage::delete("public/foto-profile/" . basename($user->foto));
        }

        $fotoUpload = $request->file('foto');
        $pathFoto = $fotoUpload->storeAs("public/foto-profile", $fotoUpload->getClientOriginalName());
      } else {
        $pathFoto = $user->foto;
      }

      $dataToUpdate = $request->only([
        "role_id", "username", "email", "password", "nama_lengkap", "foto", "jenis_identitas",
        "nomor_identitas", "jenis_kelamin", "tempat_lahir", "tanggal_lahir",
        "provinsi", "kabupaten_kota", "kecamatan", "kelurahan", "alamat",
        "no_telp", "pekerjaan", "is_login", "is_active"
      ]);
      $dataToUpdate['foto'] = $pathFoto;

      if (!empty($dataToUpdate['password'])) {
        $dataToUpdate['password'] = Hash::make($dataToUpdate['password']);
      }

      $user->update($dataToUpdate);

      return response()->json(['success' => true, 'message' => 'Profile berhasil diupdate', 'data' => $user]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function activateAccount($userId)
  {
    if (auth()->user()->role->nama !== 'Admin Utama') {
      return response()->json(['message' => 'Akses ditolak'], 403);
    }

    try {
      $user = User::find($userId);

      if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
      }

      $user->is_active = 'Y';
      $user->save();

      $data['email'] = $user->email;
      $data['title'] = 'Aktivasi Akun';
      $data['body'] = 'Akun anda berhasil diaktivasi, silahkan login kembali !';

      Mail::send('emails.aktivasi-akun', ['data' => $data], function ($message) use ($data) {
        $message->to($data['email'])->subject($data['title']);
      });

      return response()->json(['success' => true, 'message' => 'Selamat, segera cek email anda !']);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function users(Request $request)
  {
    try {
      if (auth()->user()->role->nama === 'Pemohon') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $userId = $request->query('user_id');
      $role = $request->query('role');

      $query = User::with('role');
      $message = "Semua user";

      if ($userId) {
        $query->where('id', $userId);
        $message = "Filter user dengan id = " . $userId;
      }

      if ($role) {
        $query->whereHas('role', function ($query) use ($role) {
          $query->where('nama', $role);
        });
        $message = "Filter user dengan role = " . $role;
      }

      $users = $query->get();

      return response()->json(['success' => true, 'message' => $message, 'data' => $users]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function roles(Request $request)
  {
    try {
      if (auth()->user()->role->nama !== 'Admin Utama') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $nama = $request->query('nama');

      $query = Role::query();
      $message = "Semua role";

      if ($nama) {
        $query->where('nama', $nama);
        $message = "Filter role dengan nama = " . $nama;
      }

      $roles = $query->get();

      return response()->json(['success' => true, 'message' => $message, 'data' => $roles]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function setRole(Request $request, User $user)
  {
    try {
      if (auth()->user()->role->nama !== 'Admin Utama') {
        return response()->json(['message' => 'Akses ditolak'], 403);
      }

      $validatedData = $request->validate([
        'role_id' => 'required|exists:role,id',
      ]);

      $role = Role::find($validatedData['role_id']);

      if (!$role) {
        return response()->json(['success' => false, 'message' => 'Role tidak valid'], 400);
      }

      $user->role()->associate($role);
      $user->save();

      return response()->json(['success' => true, 'message' => 'Role berhasil diubah', 'data' => $user]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}
