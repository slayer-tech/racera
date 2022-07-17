<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Message;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MessageController extends Controller
{
    public function store(Request $request)
    {
        $user_id = $request->user()->id;
        $recipient_id = $request->recipient_id;

        $chatcontroller = new ChatController();
        $chatcontroller->store($recipient_id);

        $profile = Profile::find($recipient_id);
        $chats = $profile->chats;

        foreach ($chats as $chat) {
            $profiles = $chat->profiles;
            $first_profile_id = $profiles[0]->id;
            $second_profile_id = $profiles[1]->id;

            if ($first_profile_id == $recipient_id && $second_profile_id == $user_id
                || $first_profile_id == $user_id && $second_profile_id == $recipient_id) {
                $message = Message::create([
                    'chat_id' => $chat->id,
                    'profile_id' => $user_id,
                    'content' => $request->input("content")
                ]);

                return response()->json($message);
            }
        }

        return response()->json([
            'errors' => [
                'Chat does not exist'
            ]
        ], 404);
    }
}
