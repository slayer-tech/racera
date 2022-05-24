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
    public function index()
    {
        $user = Auth::user();
        $user->profile;
        $user->cars;

        return response()->json($user);
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
     * @return int
     */
    public function find(string $name)
    {
        return 123;
    }
}
