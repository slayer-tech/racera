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

    public function show(int $id)
    {
        $upgrade = Upgrade::find($id);

        return response()->json($upgrade);
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

        $profile->upgrade()->attach($id);

        return response()->json([
            'message' => 'The upgrade was successfully bought'
        ]);
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

        $car = Upgrade::find($id);

        $profile->money += $car->price;
        $profile->save();

        $this->profile->cars()->detach($id);

        return response()->json([
            'message' => 'The upgrade was successfully sold'
        ]);
    }
}
