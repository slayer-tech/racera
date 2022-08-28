<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Upgrade;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UpgradeController extends Controller
{
    public function index()
    {
        $upgrades = Upgrade::all();

        return response()->json($upgrades);
    }

    public function buy(int $id)
    {
        $profile = Profile::find(Auth::user()->id);

        if ($profile->upgrades->find($id)) {
            return response()->json([
                'errors' => [
                    'The upgrade is already bought'
                ]
            ], 400);
        }

        $upgrade = Upgrade::find($id);

        if ($profile->money < $upgrade->price) {
            return response()->json([
                'errors' => [
                    'Not enough money to buy the upgrade'
                ]
            ]);
        }

        $profile->money -= $upgrade->price;
        $profile->save();

        $profile->upgrades()->attach($id);

        return response()->json([
            'message' => 'The upgrade was successfully bought'
        ], 201);
    }

    public function sell(int $id)
    {
        $profile = Profile::find(Auth::user()->id);

        if (!$profile->upgrades->find($id)) {
            return response()->json([
                'errors' => [
                    'This upgrade has not been bought'
                ]
            ], 400);
        }

        $upgrade = Upgrade::find($id);

        $profile->money += $upgrade->price;
        $profile->save();

        $profile->upgrades()->detach($id);

        return response()->json(null, 204);
    }
}
