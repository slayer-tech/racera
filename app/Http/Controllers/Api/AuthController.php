<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function signup(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string|unique:users',
            'email' => 'required|string|unique:users',
            'password' => 'required|string|confirmed'
        ]);

        if ($validator->failed()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        User::create([
            'login' => $request->login,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'nick' => $request->login
        ]);

        if(!Auth::attempt($request->all())) {
            return response()->json([
                'errors' => [
                    'Error! Try later'
                ]
            ], 500);
        }

        Profile::create([
            'name' => $request->login,
        ]);

        $token = Auth::user()->createToken(config('app.name'))->plainTextToken;

        return response()->json($token);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        if ($validator->failed()) {
            return response()->json(['errors' => $validator->errors()->all()], 400);
        }

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';

        $request->merge([$field => $login]);
        $credentials = $request->only($field, 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'errors' => 'Incorrect password or email'
            ], 400);
        }

        $token = Auth::user()->createToken(config('app.name'))->plainTextToken;

        return response()->json($token);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token, $key) {
            $token->revoke();
        });

        return response()->json([
            'message' => 'Successfully logged out',
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user());
    }
}
