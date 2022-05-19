<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Clan;
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
        $clans = Clan::all();

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
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|unique:clans',
            'description' => 'string',
            'avatar' => 'string'
        ]);

        if ($validator->failed()) {
            return response()->json(['errors' => $validator->errors()->all()], 422);
        }

        Clan::create([
            'name' => $request->name,
            'description' => $request->description,
            'avatar' => $request->avatar
        ]);

        return response()->json([
            'message' => 'Successfully created clan'
        ]);
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
    public function update(Request $request, int $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'string',
            'avatar' => 'string'
        ]);

        $arr = $request->all();

        if ($arr['avatar'] === null)
            unset($arr['avatar']);

        if ($validator->failed())
            return response()->json(['errors' => $validator->errors()->all()], 422);

        $clan = Clan::find($id)->update($arr);

        return response()->json($clan);
    }
}
