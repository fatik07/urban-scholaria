<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\PasswordReset;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ResetPasswordController extends Controller
{
  public function sendResetLink(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 400);
    }

    $passwordReset = PasswordReset::where('email', $request->email)->first();

    if ($passwordReset) {
      $token = $passwordReset->token;
    } else {
      // Jika tidak, buat token baru
      $token = Str::random(60);
      PasswordReset::create([
        'email' => $request->email,
        'token' => $token,
        'created_at' => Carbon::now(),
      ]);
    }

    // Kirim email reset password di sini
    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    // Define the data for the email
    $data = [
      'user' => $user,
      'resetLink' => route('reset-password.show', ['token' => $token]),
    ];

    // Kirim email reset password di sini
    Mail::send('emails.reset-password', ['data' => $data], function ($message) use ($data) {
      $message->to($data['user']->email)
        ->subject('Reset Password');
    });

    return response()->json(['message' => 'Reset password link has been sent to your email']);
  }

  public function showResetForm($token)
  {
    return response()->json([
      'token' => $token,
    ]);
  }

  public function sendResetPassword(Request $request, $token)
  {
    $request->validate([
      'password' => 'required|min:8|confirmed',
    ]);

    // Periksa apakah token valid dan belum kedaluwarsa
    $passwordReset = PasswordReset::where('token', $token)->first();

    if (!$passwordReset || $passwordReset->isExpired()) {
      return response()->json(['message' => 'Token tidak valid atau telah kedaluwarsa.'], 400);
    }

    // Ubah kata sandi pengguna
    $user = User::where('email', $passwordReset->email)->first();

    if (!$user) {
      return response()->json(['message' => 'User not found'], 404);
    }

    $user->update([
      'password' => Hash::make($request->password),
    ]);

    // Hapus token dari tabel password_resets setelah penggantian kata sandi berhasil
    PasswordReset::whereToken($token)->delete();

    return response()->json(['message' => 'Kata sandi telah berhasil diubah.']);
  }
}
