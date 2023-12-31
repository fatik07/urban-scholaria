<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\ChatController;
use App\Http\Controllers\API\ContactController;
use App\Http\Controllers\API\FeedbackController;
use App\Http\Controllers\API\PdfController;
use App\Http\Controllers\API\PushNotificationController;
use App\Http\Controllers\API\ResetPasswordController;
use App\Http\Controllers\API\SuratController;
use App\Http\Controllers\API\SuratDokumenController;
use App\Http\Controllers\API\SuratJenisController;
use App\Http\Controllers\API\SuratSyaratController;
use App\Http\Controllers\API\SurveyorController;
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
    // Route::get("aktivasi-akun/{token}", "activateAccount")->name('activate.account');

    Route::middleware("auth:sanctum")->group(function () {
        Route::get("profile", "getProfile");
        Route::post("update-profile", "updateProfile");

        Route::post("aktivasi-akun/{userId}", "activateAccount");

        Route::post("tolak-aktivasi-akun/{userId}", "activateAccountReject");

        Route::post("logout", "logout");

        Route::get("users", "users");

        Route::get("user/{userId}", "detailUser");

        Route::get("roles", "roles");

        Route::post("set-role/{user}", "setRole");
    });
});

Route::controller(ResetPasswordController::class)->group(function () {
    // Route::post("send-reset-link", "sendResetLink");
    // Route::get("reset-password/{token}", "showResetForm")->name('reset-password.show');
    // Route::post("reset-password/{token}", "sendResetPassword")->name('reset-password.send');
    Route::post("send-otp", "sendOtp");
    Route::post("verify-otp", "verifyOtp");
    Route::get("send-otp-again", "sendOtpAgain");
    Route::post("reset-password", "sendResetPassword");
});

Route::controller(SuratController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::get("surat", "index"); // bisa menggunakan param khusus status
        Route::post("surat", "create");
        Route::patch("surat/{id}", "update"); // jika ditolak validasinya

        Route::get("surat/{userId}", "getSuratByUserId");
        Route::get("surat/{suratJenisId}/syarat", "getSyaratBySuratJenis");

        Route::post("surat/{suratId}/surat-jenis/{suratJenisId}/upload-dokumen/{suratSyaratId}", "uploadDokumenBySuratSyaratId");
        Route::patch("surat/{suratId}/surat-jenis/{suratJenisId}/upload-dokumen/{suratSyaratId}", "updateDokumenBySuratSyaratId");

        Route::patch("surat/{suratId}/surat-diajukan", "suratDiajukan");

        // validasi oleh operator
        Route::patch("surat/{suratId}/terima-operator", "terimaVerifikasiOperator");
        Route::patch("surat/{suratId}/tolak-operator", "tolakVerifikasiOperator");
        Route::patch("surat/{suratId}/tolak-operator-baru", "tolakVerifikasiOperatorNew");

        // verifikasi oleh verifikator
        Route::patch("surat/{suratId}/terima-verifikator", "terimaVerifikasiVerifikator");
        Route::patch("surat/{suratId}/tolak-verifikator", "tolakVerifikasiVerifikator");
        Route::patch("surat/{suratId}/tolak-verifikator-baru", "tolakVerifikasiVerifikatorNew");

        Route::post("surat/{suratId}/set-jadwal-survey", "setJadwalSurvey");

        // verifikasi oleh surveyor
        Route::post("survey/{surveyId}/set-hasil-survey", "terimaHasilSurvey");

        // verifikator
        Route::post("surat/{suratId}/terima-hasil-survey", "terimaVerifikasiSurvey");
        Route::post("surat/{suratId}/tolak-hasil-survey", "tolakVerifikasiSurvey");

        // verifikasi oleh kepala dinas
        Route::post("surat/{suratId}/terima-kepala-dinas", "terimaVerifikasiHasilSurvey");
        Route::post("surat/{suratId}/tolak-kepala-dinas", "tolakVerifikasiHasilSurvey");

        // verifikator
        Route::post("surat/{suratId}/terima-hasil-kepala-dinas", "terimaValidasiSurveyKepalaDinas");
        Route::post("surat/{suratId}/tolak-hasil-kepala-dinas", "tolakValidasiSurveyKepalaDinas");
    });
});

Route::controller(SurveyorController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::get("surveyors", "index");
    });
});

Route::controller(FeedbackController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::get("feedback-pemohon", "index");
        Route::post("feedback-pemohon", "create");
    });
});

Route::controller(ChatController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::post('start-chat',  'startChat');
        Route::post('send-message/{room_id}',  'sendMessage');
        Route::get('get-chat-list',  'getChatList');
        Route::get('get-chat-room/{room_id}',  'getChatRoom');
    });
});

Route::controller(SuratJenisController::class)->group(function () {
    Route::get("surat-jenis", "index");
    Route::post("surat-jenis", "create");
    Route::get("surat-jenis/{id}", "show");
    Route::patch("surat-jenis/{id}", "update");
    Route::delete("surat-jenis/{id}", "destroy");
});

Route::controller(SuratDokumenController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::get("surat-dokumen", "index");
        Route::post("surat-dokumen", "create");
        Route::get("surat-dokumen/{id}", "show");
        Route::patch("surat-dokumen/{id}", "update");
        Route::delete("surat-dokumen/{id}", "destroy");
    });
});

Route::controller(SuratSyaratController::class)->group(function () {
    Route::get("surat-syarat", "index");
    Route::post("surat-syarat", "create");
    Route::get("surat-syarat/{id}", "show");
    Route::patch("surat-syarat/{id}", "update");
    Route::delete("surat-syarat/{id}", "destroy");
});

Route::controller(PdfController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::get('tracking-surat', 'trackingSurat');
    });
    Route::get('surat/{surat_id}/cetak-kwitansi', 'cetakKwitansi');
    Route::get('surat/{surat_id}/cetak-surat', 'cetakSurat');
    Route::get('surat/{surat_id}/cetak-surat-legalitas', 'cetakLegalitas');
});

Route::controller(PushNotificationController::class)->group(function () {
    Route::middleware("auth:sanctum")->group(function () {
        Route::get('notifikasi', 'index');
        Route::get('notifikasi/{id}/mark-as-seen', 'markAsSeen');
    });
});

Route::post('/hubungi-kami', [ContactController::class, 'sendContact']);
