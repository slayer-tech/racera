<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return response()->json(Auth::user()->profile);
    }

    public function update(Request $request)
    {
        Profile::find($request->user()->id)->update($request->all());

        return response()->json([
            'message' => 'Successfully update user data'
        ]);
    }
}
