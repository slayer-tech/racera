<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index()
    {
        $cars = Car::all();

        return response()->json($cars);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function buy(int $id)
    {
        // ????

        $profile = Profile::find(Auth::user()->id);

        if ($profile->cars->find($id)) {
            return response()->json([
                'errors' => [
                    'The car is already bought'
                ]
            ], 400);
        }

        $profile->cars()->attach($id);

        return response()->json([
            'message' => 'The car was successfully bought'
        ]);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function sell(int $id)
    {
        $profile = Profile::find(Auth::user()->id);

        if (!$profile->cars->find($id)) {
            return response()->json([
                'errors' => [
                    'This car has not been bought'
                ]
            ], 400);
        }

        $profile->cars()->detach($id);

        return response()->json([
            'message' => 'The car was successfully sold'
        ]);
    }
}
