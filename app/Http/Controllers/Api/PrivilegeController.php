<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Privilege;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrivilegeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $privileges = Privilege::all();

        return response()->json($privileges);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $id)
    {
        $privilege = Privilege::find($id);

        if (!$privilege) {
            return response()->json([
                'errors' => [
                        'Privilege does not exist'
                    ]
            ], 404);
        }

        return response()->json($privilege);
    }

    public function buy(Request $request, int $id)
    {
        $privilege = Privilege::find($id);

        if (!$privilege) {
            return response()->json([
                'errors' => [
                    'Privilege does not exist'
                ]
            ], 404);
        }

        $profile = Profile::find(Auth::user()->id);

        if ($privilege->priority <= $profile->privilege->priority) {
            return response()->json([
                'errors' => [
                        'You cannot buy this privilege'
                    ]
            ], 400);
        }

        $profile->update([
                'privilege_id' => $id
            ]);

        return response()->json([
            'messages' => [
                'Successfully bought privilege'
            ],
        ]);
    }
}
