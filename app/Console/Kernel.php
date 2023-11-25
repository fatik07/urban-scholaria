<?php

namespace App\Console;

use App\Models\Notifikasi;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
  /**
   * Define the application's command schedule.
   *
   * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
   * @return void
   */
  protected function schedule(Schedule $schedule)
  {
    $schedule->call(function () {
      DB::table('surat')
        ->where('status', 'Verifikasi Operator')
        ->where('is_terlambat', 'N')
        ->where('updated_at', '<', now()->subDay())
        ->update(['is_terlambat' => 'Y', 'updated_at' => now()]);

      $surat = DB::table('surat')
        ->where('status', 'Verifikasi Operator')
        ->where('is_terlambat', 'Y')
        ->where('updated_at', '>=', now()->subDay())
        ->first();

      if ($surat) {
        $userRoleIds = [1, 2];
        $users = DB::table('user')
          ->whereIn('role_id', $userRoleIds)
          ->get();

        foreach ($users as $user) {
          Notifikasi::create([
            'user_id' => $user->id,
            'judul' => 'Keterlambatan untuk memverifikasi',
            'deskripsi' => 'Kami memberitahukan bahwa surat dengan nomor ' . $surat->id . ' mengalami keterlambatan verifikasi oleh operator.',
            'is_seen' => 'N',
          ]);
        }
      }
    })->daily();

    $schedule->call(function () {
      DB::table('surat')
        ->where('status', 'Verifikasi Verifikator')
        ->where('is_terlambat', 'N')
        ->where('updated_at', '<', now()->subDay(5))
        ->update(['is_terlambat' => 'Y', 'updated_at' => now()]);

      $surat = DB::table('surat')
        ->where('status', 'Verifikasi Verifikator')
        ->where('is_terlambat', 'Y')
        ->where('updated_at', '>=', now()->subDay())
        ->first();

      if ($surat) {
        $userRoleIds = [1, 2];
        $users = DB::table('user')
          ->whereIn('role_id', $userRoleIds)
          ->get();

        foreach ($users as $user) {
          Notifikasi::create([
            'user_id' => $user->id,
            'judul' => 'Keterlambatan untuk memverifikasi',
            'deskripsi' => 'Kami memberitahukan bahwa surat dengan nomor ' . $surat->id . ' mengalami keterlambatan verifikasi oleh verifikator.',
            'is_seen' => 'N',
          ]);
        }
      }
    })->daily();

    $schedule->call(function () {
      DB::table('surat')
        ->where('status', 'Penjadwalan Survey')
        ->where('is_terlambat', 'N')
        ->where('updated_at', '<', now()->subDay(3))
        ->update(['is_terlambat' => 'Y', 'updated_at' => now()]);

      $surat = DB::table('surat')
        ->where('status', 'Penjadwalan Survey')
        ->where('is_terlambat', 'Y')
        ->where('updated_at', '>=', now()->subDay())
        ->first();

      if ($surat) {
        $userRoleIds = [1, 2];
        $users = DB::table('user')
          ->whereIn('role_id', $userRoleIds)
          ->get();

        foreach ($users as $user) {
          Notifikasi::create([
            'user_id' => $user->id,
            'judul' => 'Keterlambatan untuk memverifikasi',
            'deskripsi' => 'Kami memberitahukan bahwa surat dengan nomor ' . $surat->id . ' mengalami keterlambatan untuk jadwal survey.',
            'is_seen' => 'N',
          ]);
        }
      }
    })->daily();

    $schedule->call(function () {
      DB::table('surat')
        ->where('status', 'Verifikasi Hasil Survey')
        ->where('is_terlambat', 'N')
        ->where('updated_at', '<', now()->subDay(2))
        ->update(['is_terlambat' => 'Y', 'updated_at' => now()]);

      $surat = DB::table('surat')
        ->where('status', 'Verifikasi Hasil Survey')
        ->where('is_terlambat', 'Y')
        ->where('updated_at', '>=', now()->subDay())
        ->first();

      if ($surat) {
        $userRoleIds = [1, 2];
        $users = DB::table('user')
          ->whereIn('role_id', $userRoleIds)
          ->get();

        foreach ($users as $user) {
          Notifikasi::create([
            'user_id' => $user->id,
            'judul' => 'Keterlambatan untuk memverifikasi',
            'deskripsi' => 'Kami memberitahukan bahwa surat dengan nomor ' . $surat->id . ' mengalami keterlambatan verifikasi hasil survey.',
            'is_seen' => 'N',
          ]);
        }
      }
    })->daily();

    $schedule->call(function () {
      DB::table('surat')
        ->where('status', 'Verifikasi Kepala Dinas')
        ->where('is_terlambat', 'N')
        ->where('updated_at', '<', now()->subDay(3))
        ->update(['is_terlambat' => 'Y', 'updated_at' => now()]);

      $surat = DB::table('surat')
        ->where('status', 'Verifikasi Kepala Dinas')
        ->where('is_terlambat', 'Y')
        ->where('updated_at', '>=', now()->subDay())
        ->first();

      if ($surat) {
        $userRoleIds = [1, 2];
        $users = DB::table('user')
          ->whereIn('role_id', $userRoleIds)
          ->get();

        foreach ($users as $user) {
          Notifikasi::create([
            'user_id' => $user->id,
            'judul' => 'Keterlambatan untuk memverifikasi',
            'deskripsi' => 'Kami memberitahukan bahwa surat dengan nomor ' . $surat->id . ' mengalami keterlambatan verifikasi oleh kepala dinas.',
            'is_seen' => 'N',
          ]);
        }
      }
    })->daily();
  }

  /**
   * Register the commands for the application.
   *
   * @return void
   */
  protected function commands()
  {
    $this->load(__DIR__ . '/Commands');

    require base_path('routes/console.php');
  }
}
