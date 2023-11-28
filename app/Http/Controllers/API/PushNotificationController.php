<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notifikasi;
use Illuminate\Http\Request;

class PushNotificationController extends Controller
{
  public static function sendMessage($userid, $title, $message)
  {
    $heading = array(
      "en" => $title
    );

    $content = array(
      "en" => $message
    );

    $fields = array(
      'app_id' => "ba028071-8839-4f7b-9e56-c0b398e8e526",
      'included_segments' => array('All'),
      'data' => array("userid" => $userid),
      'contents' => $content,
      'headings' => $heading
    );

    $fields = json_encode($fields);
    // print("\nJSON sent:\n");
    // print($fields);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://onesignal.com/api/v1/notifications");
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
      'Content-Type: application/json; charset=utf-8',
      'Authorization: Basic ZmMwZDdkNjktMDdhZC00ZDRkLWE1MDUtMGRlNGE2Y2IwMWRi'
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
    curl_setopt($ch, CURLOPT_HEADER, FALSE);
    curl_setopt($ch, CURLOPT_POST, TRUE);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

    curl_exec($ch);
    curl_close($ch);

    Notifikasi::create([
      'user_id' => $userid,
      'judul' => $title,
      'deskripsi' => $message,
      'is_seen' => 'N'
    ]);
  }
}
