<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use App\Models\Role;
use App\Models\User;
use App\Models\UserNotifikasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class AuthController extends Controller
{
  public function register(Request $request)
  {
    try {
      $validator = Validator::make($request->all(), [
        // "username" => "required|string|max:255",
        "email" => "required|email|unique:user,email",
        "password" => "required|string|min:8",
        "nama_lengkap" => "nullable|string|max:255",
        "foto" => "nullable|mimes:jpg,jpeg,png,gif",
        "ktp_paspor" => "nullable|mimes:jpg,jpeg,png,gif",
        "jenis_identitas" => "nullable|in:KTP,Paspor",
        "nomor_identitas" => "nullable|numeric|regex:/^(\d{16})$/|unique:user,nomor_identitas",
        "jenis_kelamin" => "nullable|in:Laki-Laki,Perempuan",
        "tempat_lahir" => "nullable|string|max:100",
        "tanggal_lahir" => "nullable|date",
        "provinsi" => "nullable|string|max:100",
        "kabupaten_kota" => "nullable|string|max:100",
        "kecamatan" => "nullable|string|max:100",
        "kelurahan" => "nullable|string|max:100",
        "alamat" => "nullable|string",
        "no_telp" => "nullable|numeric|regex:/^\d{11,13}$/|unique:user,no_telp",
        "pekerjaan" => "nullable|string|max:100",
        "is_login" => "nullable|in:Y,N",
        "is_active" => "nullable|in:Y,N",
      ]);

      if ($validator->fails()) {
        return response()->json($validator->errors());
      }

      //foto
      if ($request->hasFile('foto')) {
        $fotoUpload = $request->file('foto');
        $pathFoto = $fotoUpload->storeAs("uploads/foto-profile", $fotoUpload->getClientOriginalName());
      } else {
        $pathFoto = null;
      }

      //ktp_paspor
      if ($request->hasFile('ktp_paspor')) {
        $ktpPasporUpload = $request->file('ktp_paspor');
        $pathKtpPaspor = $ktpPasporUpload->storeAs("uploads/foto-ktp-paspor", $ktpPasporUpload->getClientOriginalName());
      } else {
        $pathKtpPaspor = null;
      }

      $user = User::create([
        'role_id' => 9,
        // 'username' => $request->username,
        'email' => $request->email,
        'password' => Hash::make($request->password),
        'nama_lengkap' => $request->nama_lengkap,
        'foto' => $pathFoto,
        'ktp_paspor' => $pathKtpPaspor,
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

      $adminDinas = User::where('role_id', 2)->first();

      if ($adminDinas) {
        // Buat notifikasi sementara
        Notifikasi::create([
          'user_id' => $adminDinas->id,
          'judul' => 'Pemberitahuan Aktivasi Akun',
          'deskripsi' => 'Mohon segera aktivasi akun dari email ' . $user->email . ' agar bisa login.',
          'is_seen' => 'N'
        ]);
      }

      // $tokenRecord = DB::table('personal_access_tokens')
      //   ->where('tokenable_id', $user->id)
      //   ->first();

      // if (!$tokenRecord) {
      //   return response()->json(['success' => false, 'message' => 'Token tidak ditemukan.']);
      // }

      // $tokenNew = $tokenRecord->token;

      // $data = [
      //   'user' => $user,
      //   'token' => $tokenNew,
      // ];

      // Mail::send('emails.aktivasi-akun', ['data' => $data], function ($message) use ($data) {
      //   $message->to($data['user']->email)
      //     ->subject('Aktivasi Akun');
      // });

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

      $user = User::with('role')->where('email', $request->email)->firstOrFail();

      if ($user->is_active === 'N') {
        return response()->json(['message' => 'Akun Anda belum diverify, segera cek email anda.'], 401);
      }

      // UserNotifikasi::where('user_id', $user->id)->delete();

      // // Update atau tambahkan token
      // $onesignalToken = $request->input('onesignal_token');

      // UserNotifikasi::create([
      //   'user_id' => $user->id,
      //   'onesignal_token' => $onesignalToken,
      // ]);

      $user->is_login = 'Y';
      $user->save();

      $token = $user->createToken('auth_token')->plainTextToken;

      return response()->json([
        'success' => true,
        'message' => 'Login berhasil dilakukan',
        'data' => $user,
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
        'email' => 'string|nullable|email|unique:user,email,' . $user->id,
        'password' => 'nullable|string|min:8',
        'nama_lengkap' => 'string|max:255|nullable',
        "foto" => "nullable|mimes:jpg,jpeg,png,gif",
        "ktp_paspor" => "nullable|mimes:jpg,jpeg,png,gif",
        'jenis_identitas' => 'nullable|in:KTP,Paspor',
        'nomor_identitas' => 'nullable|numeric|regex:/^(\d{16})$/|unique:user,nomor_identitas,' . $user->id,
        'jenis_kelamin' => 'nullable|in:Laki-Laki,Perempuan',
        'tempat_lahir' => 'nullable|string|max:100',
        'tanggal_lahir' => 'nullable|date',
        'provinsi' => 'nullable|string|max:100',
        'kabupaten_kota' => 'nullable|string|max:100',
        'kecamatan' => 'nullable|string|max:100',
        'kelurahan' => 'nullable|string|max:100',
        'alamat' => 'nullable|string',
        'no_telp' => 'nullable|numeric|regex:/^\d{11,13}$/|unique:user,no_telp,' . $user->id,
        'pekerjaan' => 'nullable|string|max:100',
        'is_login' => 'nullable|in:Y,N',
        'is_active' => 'nullable|in:Y,N'
      ]);

      if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 400);
      }

      //foto
      if ($request->hasFile('foto')) {
        if ($user->foto) {
          Storage::delete("uploads/foto-profile/" . basename($user->foto));
        }

        $fotoUpload = $request->file('foto');
        $pathFoto = $fotoUpload->storeAs("uploads/foto-profile", $fotoUpload->getClientOriginalName());
      } else {
        $pathFoto = $user->foto;
      }

      // ktp
      if ($request->hasFile('ktp_paspor')) {
        if ($user->ktp_paspor) {
          Storage::delete("uploads/foto-ktp-paspor/" . basename($user->ktp_paspor));
        }

        $ktpPasporUpload = $request->file('ktp_paspor');
        $pathKtpPaspor = $ktpPasporUpload->storeAs("uploads/foto-ktp-paspor", $ktpPasporUpload->getClientOriginalName());
      } else {
        $pathKtpPaspor = $user->ktp;
      }

      $dataToUpdate = $request->only([
        "role_id", "email", "username", "password", "nama_lengkap", "foto", "ktp_paspor", "jenis_identitas",
        "nomor_identitas", "jenis_kelamin", "tempat_lahir", "tanggal_lahir",
        "provinsi", "kabupaten_kota", "kecamatan", "kelurahan", "alamat",
        "no_telp", "pekerjaan", "is_login", "is_active"
      ]);
      $dataToUpdate['foto'] = $pathFoto;
      $dataToUpdate['ktp_paspor'] = $pathKtpPaspor;

      if (!empty($dataToUpdate['password'])) {
        $dataToUpdate['password'] = Hash::make($dataToUpdate['password']);
      }

      $user->update($dataToUpdate);

      return response()->json(['success' => true, 'message' => 'Profile berhasil diupdate', 'data' => $user]);
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  // public function activateAccount($token)
  // {
  //   try {
  //     $personalAccessToken = DB::table('personal_access_tokens')
  //       ->where('token', $token)
  //       ->first();

  //     if (!$personalAccessToken) {
  //       return response()->json(['message' => 'Token aktivasi tidak valid.'], 404);
  //     }

  //     $user = DB::table('user')
  //       ->where('id', $personalAccessToken->tokenable_id)
  //       ->first();

  //     if (!$user) {
  //       return response()->json(['message' => 'User tidak ditemukan.'], 404);
  //     }

  //     // Update status aktivasi menjadi 'Y'
  //     DB::table('user')
  //       ->where('id', $user->id)
  //       ->update(['is_active' => 'Y']);

  //     return response()->json(['message' => 'Akun berhasil diaktifkan.'], 200);
  //   } catch (\Exception $e) {
  //     return response()->json(['success' => false, 'message' => $e->getMessage()]);
  //   }
  // }

  public function activateAccount($userId)
  {
    if (auth()->user()->role->nama !== 'Admin Dinas') {
      return response()->json(['message' => 'Akses ditolak'], 403);
    }

    try {
      $user = User::find($userId);

      if (!$user) {
        return response()->json(['message' => 'User tidak ditemukan'], 404);
      }

      $user->is_active = 'Y';
      $user->save();

      $data = [
        'user' => $user,
      ];

      Mail::send('emails.aktivasi-akun', ['data' => $data], function ($message) use ($data) {
        $message->to($data['user']->email)
          ->subject('Aktivasi Akun');
      });

      return response()->json(['message' => 'Akun berhasil diaktifkan.'], 200);
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
