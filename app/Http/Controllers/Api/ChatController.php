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

            if ($first_profile->id == $user_id) {
                $profile = $second_profile;
            } else {
                $profile = $first_profile;
            }

            $result["$profile->id"] = [
                "id" => $profile->id,
                "name" => $profile->name,
                "avatar" => $profile->avatar,
                "chat_id" => $profile->pivot->chat_id,
            ];
        }

        return response()->json($result);
    }

    public function show($recipient_id)
    {
        $user_id = Auth::user()->id;

        $profile = Profile::find($user_id);
        $recipient_profile = Profile::find($recipient_id);

        if (!isset($profile)) {
             return response()->json([
                 'errors' => [
                     "User does not exist"
                 ]
            ], 404);
        }

        $chats = $profile->chats();

        foreach ($chats as $chat) {
            $profiles = $chat->profiles;

            if ($profiles[0]->id == $recipient_id && $profiles[1]->id == $user_id
                || $profiles[0]->id == $user_id && $profiles[1]->id == $recipient_id) {
                return response()->json([
                    'recipient' => [
                        'id' => $recipient_profile->id,
                        'name' => $recipient_profile->name,
                        'avatar' => $recipient_profile->avatar
                    ],
                    'messages' => $chat->messages
                ]);
            }
        }

        return response()->json([
            'recipient' => [
                'id' => $recipient_profile->id,
                'name' => $recipient_profile->name,
                'avatar' => $recipient_profile->avatar
            ]
        ]);
    }

    public function store(Request $request, $recipient_id) {
        $user_id = Auth::user()->id;

        if ($this->isExists($user_id, $recipient_id)) {
            return;
        }

        $chat = Chat::create();
        $chat->profiles()->attach($user_id);
        if ($user_id != $recipient_id)
            $chat->profiles()->attach($recipient_id);
    }

    private function isExists($user_id, $recipient_id)
    {
        $profile = Profile::find($user_id);
        $recipient_profile = Profile::find($recipient_id);
        $chats = $profile->chats;

        foreach ($chats as $chat) {
            $profiles = $chat->profiles;

            if ($profiles[0]->id == $recipient_id && $profiles[1]->id == $user_id
                || $profiles[0]->id == $user_id && $profiles[1]->id == $recipient_id) {
                return true;
            }
        }

        return false;
    }
}
