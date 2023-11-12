<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\SuratController;
use App\Http\Controllers\API\SuratDokumenController;
use App\Http\Controllers\API\SuratJenisController;
use App\Http\Controllers\API\SuratSyaratController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
  Route::post("register", "register");
  Route::post("login", "login");

  Route::middleware("auth:sanctum")->group(function () {
    Route::get("profile", "getProfile");
    Route::patch("update-profile", "updateProfile");

    Route::post("aktivasi-akun/{userId}", "activateAccount");

    Route::post("logout", "logout");
  });
});

Route::controller(SuratController::class)->group(function () {
  Route::middleware("auth:sanctum")->group(function () {
    Route::get("surat", "index"); // bisa menggunakan param khusus status
    Route::post("surat", "create");
    // Route::patch("surat/{id}", "update"); // jika ditolak validasinya

    Route::get("surat/{userId}", "getSuratByUserId");
    Route::get("surat/{suratJenisId}/syarat", "getSyaratBySuratJenis");

    Route::post("surat/{suratId}/surat-jenis/{suratJenisId}/upload-dokumen/{suratSyaratId}", "uploadDokumenBySuratSyaratId");
    // Route::patch("surat/{suratId}/surat-jenis/{suratJenisId}/upload-dokumen/{suratSyaratId}", "updateDokumenBySuratSyaratId");

    // Route::patch("surat/{suratId}/surat-selesai", "suratSelesai");

    // // validasi oleh operator
    // Route::patch("surat/{suratId}/terima-verifikasi", "terimaVerifikasiOperator");
    // Route::patch("surat/{suratId}/tolak-verifikasi", "tolakVerifikasiOperator");
  });
});

Route::controller(SuratJenisController::class)->group(function () {
  Route::middleware("auth:sanctum")->group(function () {
    Route::get("surat-jenis", "index");
    // Route::post("surat-jenis", "create");
    Route::get("surat-jenis/{id}", "show");
    Route::patch("surat-jenis/{id}", "update");
    Route::delete("surat-jenis/{id}", "destroy");
  });
});

Route::controller(SuratSyaratController::class)->group(function () {
  Route::middleware("auth:sanctum")->group(function () {
    Route::get("surat-syarat", "index");
    // Route::post("surat-syarat", "create");
    Route::get("surat-syarat/{id}", "show");
    // Route::patch("surat-syarat/{id}", "update");
    // Route::delete("surat-syarat/{id}", "destroy");
  });
});

// Route::controller(SuratDokumenController::class)->group(function () {
//   Route::middleware("auth:sanctum")->group(function () {
//     Route::get("surat-dokumen", "index");
//     Route::post("surat-dokumen", "create");
//     Route::get("surat-dokumen/{id}", "show");
//     Route::patch("surat-dokumen/{id}", "update");
//     Route::delete("surat-dokumen/{id}", "destroy");
//   });
// });
