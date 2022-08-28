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
        $request->validate([
            'login' => 'unique:users,login|required|string',
            'email' => 'unique:users,email|required|string',
            'password' => 'required|string'
        ]);

        $user = User::create([
            'login' => $request->login,
            'email' => $request->email,
            'password' => bcrypt($request->password),
        ]);

        if(!Auth::attempt($request->all())) {
            return response()->json([
                'errors' => [
                    'Error! Try later'
                ]
            ], 500);
        }

        Profile::create([
            'id' => $user->id,
            'name' => $user->login,
        ]);

        $token = Auth::user()->createToken(config('app.name'))->plainTextToken;

        return response()->json([
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password' => 'required|string'
        ]);

        $login = $request->login;
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'login';

        $request->merge([$field => $login]);
        $credentials = $request->only($field, 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json([
                'errors' => [
                    'Incorrect password or ' . $field
                ]
            ], 422);
        }

        $token = Auth::user()->createToken(config('app.name'))->plainTextToken;

        return response()->json([
            'token' => $token
        ]);
    }

    public function logout(Request $request)
    {
        $request->user()->tokens->each(function ($token, $key) {
            $token->delete();
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
