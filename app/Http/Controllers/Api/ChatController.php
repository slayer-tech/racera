<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Chat;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $user_id = Auth::user()->id;
        $profile = Profile::find($user_id);

        $chats = $profile->chats;
        $result = [];

        foreach ($chats as $chat) {
            $profiles = Chat::find($chat->id)->profiles;
            $first_profile = $profiles[0];
            $second_profile = $profiles[1];

            $profile = ($first_profile->id == $user_id) ? $second_profile : $first_profile;

            $result["$profile->id"] = [
                "id" => $profile->id,
                "name" => $profile->name,
                "avatar" => $profile->avatar
            ];
        }

        return response()->json([
            "data" => $result
        ]);
    }

    public function show($recipient_id)
    {
        $user_id = Auth::user()->id;

        $profile = Profile::findOrFail($user_id);
        $recipient_profile = Profile::findOrFail($recipient_id);

        $chat = $this->getChat($user_id, $recipient_id);

        if ($chat) {
            return response()->json([
                'data' => [
                    'recipient' => [
                        'id' => $recipient_profile->id,
                        'name' => $recipient_profile->name,
                        'avatar' => $recipient_profile->avatar
                    ],
                    'messages' => $chat->messages
                ]
            ]);
        }

        return response()->json([
            'data' => [
                'recipient' => [
                    'id' => $recipient_profile->id,
                    'name' => $recipient_profile->name,
                    'avatar' => $recipient_profile->avatar
                ]
            ]
        ]);
    }

    public function store(Request $request) {
        $user_id = Auth::user()->id;
        $recipient_id = $request->recipient_id;

        $chat = $this->getChat($user_id, $recipient_id);

        if ($chat) {
            return;
        }

        $chat = Chat::create();
        $chat->profiles()->attach($user_id);
        $chat->profiles()->attach($recipient_id);
    }

    private function getChat($user_id, $recipient_id)
    {
        $profile = Profile::find($user_id);
        $chats = $profile->chats;

        foreach ($chats as $chat) {
            $profiles = $chat->profiles;

            if ($profiles[0]->id == $recipient_id && $profiles[1]->id == $user_id
                || $profiles[0]->id == $user_id && $profiles[1]->id == $recipient_id) {
                return $chat;
            }
        }

        return false;
    }
}
