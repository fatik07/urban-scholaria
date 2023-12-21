<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\ListChat;
use App\Models\RoomChat;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class ChatController extends Controller
{
    public function startChat(Request $request)
    {
        $user = Auth::user();
        $receiverUserId = $request->input('receiver_user_id');

        $receiverUser = User::find($receiverUserId);

        if ($receiverUser) {
            if (($user->role->nama === 'Pemohon' && $receiverUser->role->nama === 'Operator') ||
                ($user->role->nama === 'Operator' && $receiverUser->role->nama === 'Pemohon') ||
                ($user->role->nama === 'Verifikator' && $receiverUser->role->nama === 'Surveyor') ||
                ($user->role->nama === 'Surveyor' && $receiverUser->role->nama === 'Verifikator')
            ) {

                $accounts = [$user->id, $receiverUserId];
                sort($accounts);

                $room = RoomChat::firstOrCreate([
                    'account' => implode('-', $accounts),
                ]);

                return response()->json(['room_id' => $room->id], 200);
            } else {
                return response()->json(['message' => 'Akses ditolak'], 403);
            }
        } else {
            return response()->json(['message' => 'Penerima pesan tidak ditemukan'], 404);
        }
    }

    public function sendMessage(Request $request, $roomId)
    {
        $user = Auth::user();
        $room = Roomchat::find($roomId);

        if (!$room) {
            return response()->json(['message' => 'Room chat not found'], 404);
        }

        $receiverUserId = $request->input('receiver_user_id');
        $receiverUser = User::find($receiverUserId);

        if (!$receiverUser) {
            return response()->json(['message' => 'Penerima pesan tidak ditemukan'], 404);
        }

        // Validasi apakah pengguna memiliki hak untuk mengirim pesan ke penerima yang ditentukan
        if (($user->role->nama === 'Pemohon' && $receiverUser->role->nama === 'Operator') ||
            ($user->role->nama === 'Operator' && $receiverUser->role->nama === 'Pemohon') ||
            ($user->role->nama === 'Verifikator' && $receiverUser->role->nama === 'Surveyor') ||
            ($user->role->nama === 'Surveyor' && $receiverUser->role->nama === 'Verifikator')
        ) {

            $accountParts = explode('-', $room->account);

            if (count($accountParts) === 2) {
                $userIdOne = $accountParts[0];
                $userIdTwo = $accountParts[1];

                $room->update([
                    'last_message' => $request->input('message'),
                    'counter_satu' => ($user->id == $userIdOne) ? $room->counter_satu + 1 : $room->counter_satu,
                    'counter_kedua' => ($user->id == $userIdTwo) ? $room->counter_kedua + 1 : $room->counter_kedua,
                ]);

                $message = ListChat::create([
                    'roomchat_id' => $room->id,
                    'account' => $user->id,
                    'message' => $request->input('message'),
                    'read' => false,
                ]);

                // PushNotificationController::sendMessage(
                //   $receiverUser->id,
                //   'Ada pesan baru',
                //   $request->input('message')
                // );

                return response()->json(['message_id' => $message->id], 201);
            } else {
                return response()->json(['message' => 'Struktur account tidak valid'], 500);
            }
        } else {
            return response()->json(['message' => 'Akses ditolak'], 403);
        }
    }

    public function getChatList(Request $request)
    {
        $user = Auth::user();

        $room = Roomchat::where(function ($query) use ($user) {
            $query->where('account', 'like', "%{$user->id}%")
                ->orWhere('account', 'like', "%{$user->id}%");
        })->get();

        $room = $room->map(function ($item) {
            $userIds = explode('-', $item->account, 2);

            $userBeforeDash = User::select('id', 'role_id', 'email', 'nama_lengkap', 'foto')->where('id', $userIds[0])->first();
            $userAfterDash = User::select('id', 'role_id', 'email', 'nama_lengkap', 'foto')->where('id', $userIds[1])->first();

            return array_merge($item->toArray(), [
                'user_before_dash' => $userBeforeDash,
                'user_after_dash' => $userAfterDash,
            ]);
        });

        return response()->json(['rooms' => $room], 200);
    }

    public function getChatRoom($roomId)
    {
        $user = Auth::user();
        $room = Roomchat::find($roomId);

        if (!$room) {
            return response()->json(['message' => 'Room chat not found'], 404);
        }

        $messages = $room->listchats()->orderBy('created_at', 'asc')->get();

        // user yang bukan login
        $otherUserId = ($user->id == explode('-', $room->account)[0]) ? explode('-', $room->account)[1] : explode('-', $room->account)[0];

        $unreadMessages = $room->listchats()->where('account', $otherUserId)->where('read', false)->get();

        foreach ($unreadMessages as $message) {
            $message->update([
                'read' => ($message->account == $user->id) ? false : true,
            ]);
        }

        $room->update([
            'counter_satu' => 0,
            'counter_kedua' => 0
        ]);

        return response()->json(['messages' => $messages], 200);
    }
}
