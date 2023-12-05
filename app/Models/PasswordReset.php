<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordReset extends Model
{
  use HasFactory;

  public $table = 'password_resets';

  protected $fillable = ['email', 'token', 'created_at'];

  public $timestamps = false;

  public function isExpired()
  {
    $expirationTime = Carbon::parse($this->created_at)->addMinutes(config('auth.passwords.users.expire'));
    return Carbon::now()->gt($expirationTime);
  }
}
