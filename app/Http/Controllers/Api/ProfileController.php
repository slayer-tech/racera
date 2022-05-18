<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    public function editDescription(Request $request)
    {
        User::find($request->user()->id)->update(['description' => $request->description]);

        return response()->json([
            'message' => 'Successfully edited description'
        ]);
    }
}
