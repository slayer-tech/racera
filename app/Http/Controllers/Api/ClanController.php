<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clan;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ClanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $clans = Clan::paginate();

        return response()->json($clans);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {
        $profile = Profile::find(Auth::user()->id);

        $request->validate([
            'name' => 'required|string|unique:clans,name',
            'description' => 'required|string',
            'avatar' => 'required|string'
        ]);

        if ($profile->clan_id) {
            return response()->json([
                'You are already in a clan'
            ]);
        }

        $clan = Clan::create([
            'name' => $request->name,
            'description' => $request->description,
            'avatar' => $request->avatar,
            'creator_id' => $profile->id
        ]);

        $profile->update([
            'clan_id' => $clan->id
        ]);

        return response()->json($clan, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $clan = Clan::find($id);
        $clan->profiles;

        return response()->json($clan);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'string',
            'avatar' => 'string'
        ]);

        $data = $request->only(['name', 'description', 'avatar']);

        foreach ($data as $key => $value) {
            if ($value === null)
                unset($data[$key]);
        }

        if ($validator->failed())
            return response()->json(['errors' => $validator->errors()->all()], 400);

        $clan = Clan::find($id);
        $clan->update($data);

        return response()->json($clan);
    }

    public function search(string $name)
    {
        $clans = Clan::where('name', 'LIKE', '%' . $name . '%')->get();

        return response()->json($clans);
    }
}
