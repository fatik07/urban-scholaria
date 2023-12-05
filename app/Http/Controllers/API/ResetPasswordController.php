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
use Mockery\Generator\StringManipulation\Pass\Pass;
use Password;

class ResetPasswordController extends Controller
{
  // public function sendResetLink(Request $request)
  // {
  //   $validator = Validator::make($request->all(), [
  //     'email' => 'required|email',
  //   ]);

  //   if ($validator->fails()) {
  //     return response()->json(['errors' => $validator->errors()], 400);
  //   }

  //   $passwordReset = PasswordReset::where('email', $request->email)->first();

  //   if ($passwordReset) {
  //     $token = $passwordReset->token;
  //   } else {
  //     $token = Str::random(60);

  //     PasswordReset::create([
  //       'email' => $request->email,
  //       'token' => $token,
  //       'created_at' => Carbon::now(),
  //     ]);
  //   }

  //   $user = User::where('email', $request->email)->first();

  //   if (!$user) {
  //     return response()->json(['message' => 'User not found'], 404);
  //   }

  //   $data = [
  //     'user' => $user,
  //     'resetLink' => route('reset-password.show', ['token' => $token]),
  //   ];

  //   // Kirim email reset password di sini
  //   Mail::send('emails.reset-password', ['data' => $data], function ($message) use ($data) {
  //     $message->to($data['user']->email)
  //       ->subject('Reset Password');
  //   });

  //   return response()->json(['message' => 'Reset password link has been sent to your email']);
  // }

  // public function showResetForm($token)
  // {
  //   return response()->json([
  //     'token' => $token,
  //   ]);
  // }

  // public function sendResetPassword(Request $request, $token)
  // {
  //   $request->validate([
  //     'password' => 'required|min:8|confirmed',
  //   ]);

  //   // Periksa apakah token valid dan belum kedaluwarsa
  //   $passwordReset = PasswordReset::where('token', $token)->first();

  //   if (!$passwordReset || $passwordReset->isExpired()) {
  //     return response()->json(['message' => 'Token tidak valid atau telah kedaluwarsa.'], 400);
  //   }

  //   $user = User::where('email', $passwordReset->email)->first();

  //   if (!$user) {
  //     return response()->json(['message' => 'User not found'], 404);
  //   }

  //   $user->update([
  //     'password' => Hash::make($request->password),
  //   ]);

  //   // Hapus token dari tabel password_resets setelah penggantian kata sandi berhasil
  //   PasswordReset::whereToken($token)->delete();

  //   return response()->json(['message' => 'Kata sandi telah berhasil diubah.']);
  // }

  public function sendOtp(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'email' => 'required|email|exists:user,email',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 400);
    }

    $user = User::where('email', $request->email)->first();

    if (!$user) {
      return response()->json(['message' => 'Email not found'], 404);
    }

    $passwordReset = PasswordReset::where('email', $request->email)->first();

    if ($passwordReset) {
      $otp = $passwordReset->token;
    } else {
      $otp = mt_rand(100000, 999999);

      PasswordReset::updateOrCreate(
        ['email' => $request->email],
        [
          'token' => $otp,
          'created_at' => Carbon::now(),
        ]
      );
    }

    // PasswordReset::updateOrCreate(
    //   ['email' => $request->email],
    //   [
    //     'token' => Hash::make($otp),
    //     'created_at' => Carbon::now(),
    //   ]
    // );

    $data = [
      'user' => $user,
      'otp' => $otp,
    ];

    Mail::send('emails.reset-password', ['data' => $data], function ($message) use ($data) {
      $message->to($data['user']->email)
        ->subject('Reset Password With OTP');
    });

    return response()->json(['message' => 'OTP has been sent to your email']);
  }

  public function verifyOtp(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'otp' => 'required|numeric|regex:/^(\d{6})$/',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 400);
    }

    $passwordReset = PasswordReset::orderBy('created_at', 'desc')->first();

    // if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(1)->isPast()) {
    //   return response()->json(['message' => 'Invalid or expired OTP'], 400);
    // }

    if ($request->otp !== $passwordReset->token) {
      return response()->json(['message' => 'Invalid OTP'], 400);
    }

    return response()->json(['message' => 'OTP verified successfully']);
  }

  public function sendOtpAgain()
  {
    $passwordReset = PasswordReset::orderBy('created_at', 'desc')->first();

    // if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(1)->isPast()) {
    //   return response()->json(['message' => 'Invalid or expired OTP'], 400);
    // }

    if ($passwordReset->token) {
      PasswordReset::whereEmail($passwordReset->email)->delete();

      $newOtp = mt_rand(100000, 999999);
      PasswordReset::updateOrCreate([
        'email' => $passwordReset->email,
        'token' => $newOtp,
        'created_at' => Carbon::now(),
      ]);
    }

    $user = User::where('email', $passwordReset->email)->first();

    $data = [
      'user' => $user,
      'otp' => $newOtp,
    ];

    Mail::send('emails.reset-password', ['data' => $data], function ($message) use ($data) {
      $message->to($data['user']->email)
        ->subject('Reset Password With OTP');
    });

    return response()->json(['message' => 'New OTP has been sent to your email']);
  }

  public function sendResetPassword(Request $request)
  {
    $validator = Validator::make($request->all(), [
      'password' => 'required|min:8|confirmed',
    ]);

    if ($validator->fails()) {
      return response()->json(['errors' => $validator->errors()], 400);
    }

    $passwordReset = PasswordReset::orderBy('created_at', 'desc')->first();

    // if (!$passwordReset || Carbon::parse($passwordReset->created_at)->addMinutes(1)->isPast()) {
    //   PasswordReset::whereEmail($passwordReset->email)->delete();
    //   return response()->json(['message' => 'Invalid or expired OTP'], 400);
    // }

    $user = User::where('email', $passwordReset->email)->first();

    if (!$user) {
      return response()->json(['message' => 'Email not found'], 404);
    }

    $user->update([
      'password' => Hash::make($request->password),
    ]);

    // Hapus token dari tabel password_resets setelah penggantian kata sandi berhasil
    PasswordReset::whereEmail($passwordReset->email)->delete();

    return response()->json(['message' => 'Kata sandi telah berhasil diubah.']);
  }
}
