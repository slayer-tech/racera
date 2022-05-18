<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function profile()
    {
        return response()->json(Auth::user()->profile);
    }

    public function editDescription(Request $request)
    {
        User::find($request->user()->id)->update(['description' => $request->description]);

        return response()->json([
            'message' => 'Successfully edited description'
        ]);
    }
}
