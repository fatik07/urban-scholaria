<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\LicensingController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\SubmissionController;
use App\Http\Controllers\API\SuratJenisController;
use App\Http\Controllers\API\VerificationController;
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

Route::controller(SuratJenisController::class)->group(function () {
  Route::middleware("auth:sanctum")->group(function () {
    Route::get("surat-jenis", "index");
    Route::post("surat-jenis", "create");
    Route::get("surat-jenis/{id}", "show");
    Route::patch("surat-jenis/{id}", "update");
    Route::delete("surat-jenis/{id}", "destroy");
  });
});
