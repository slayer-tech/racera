<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $limit = $request->limit;
        $offset = ($request->page - 1) * $limit;

        $profiles = Profile::offset($offset)->limit($limit)->get();

        return response()->json($profiles);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $profile = Profile::find($id);

        return response()->json($profile);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request)
    {
        Profile::find($request->user()->id)->update($request->all());

        return response()->json([
            'message' => 'Successfully update user data'
        ]);
    }

    /**
     * @param string $name
     * @return \Illuminate\Http\JsonResponse
     */
    public function find(string $name)
    {
        $profiles = Profile::where('name', 'LIKE', '%' . $name . '%')->get();
        $result = [];

        foreach ($profiles as $profile) {
            $result[$profile->id]['id'] = $profile->id;
            $result[$profile->id]['name'] = $profile->name;
            $result[$profile->id]['description'] = $profile->description;
            $result[$profile->id]['avatar'] = $profile->avatar;
            $result[$profile->id]['clan_id'] = $profile->clan_id;
            $result[$profile->id]['created_at'] = $profile->created_at;
        }

        return response()->json($result);
    }
}
