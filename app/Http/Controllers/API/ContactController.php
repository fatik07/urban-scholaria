<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function sendContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nama_lengkap' => 'required|string',
            'email' => 'required|email',
            'pesan' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Kirim email
        Mail::send('emails.contact', ['request' => $request], function ($message) use ($request) {
            $message->to('fatichur.r07@gmail.com', 'Helpdesk Urban')->subject('Pesan dari Hubungi Kami');
            // $message->from('Helpdesk Urban Scholaria');
        });

        return response()->json(['message' => 'Pesan telah dikirim. Terima kasih!']);
    }
}
