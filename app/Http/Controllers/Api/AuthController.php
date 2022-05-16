<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use http\Cookie;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use Laravel\Passport\Client;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);

         User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password)
        ]);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string',
            'password' => 'required|string'
        ]);

        if($validator->failed()) {
            return response()->json(['errors'=> $validator->errors()->all()], 422);
        }

        if (!Auth::attempt($request->all())) {
            return response()->json([
                'errors' => 'Password or email incorrect'
            ], 422);
        }

        $token = Auth::user()->createToken(config('app.name'))->plainTextToken;

        return response()->json($token);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();

        return response()->json([
            'message' => 'You are successfully logged out',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
