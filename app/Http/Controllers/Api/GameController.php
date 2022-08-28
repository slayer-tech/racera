<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\GameRequest;
use App\Models\Game;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class GameController extends Controller
{
    public static function create($secret_token)
    {
        $game = Game::create(["secret_token" => $secret_token]);

        return $game;
    }

    public static function end(GameRequest $request)
    {
        $game = Game::findOrFail($request->game_id);

        if ($game->secret_token != $request->secret_token) {
            return response()->json(['errors' => ['Incorrect token']], 422);
        }

        $winner = Profile::find($request->winner_id);
        $loser = Profile::find($request->loser_id);

        $game->profiles()->attach($winner, ['winner' => true]);
        $game->profiles()->attach($loser, ['winner' => false]);
    }

    public static function generateWalls(): array
    {
        $variations = [
            [0, 0, 1],
            [1, 0, 0],
            [0, 1, 0],
            [0, 1, 1],
            [1, 1, 0],
            [1, 1, 1],
        ];

        return $variations[rand(0, count($variations))];
    }
}
