<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    /**
     * @return JsonResponse
     */
    public function index(Request $request)
    {
        $cars = Car::paginate();

        return response()->json($cars);
    }

    public function show(int $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'errors' => [
                    "Car does not exist"
                ]
            ], 404);
        }

        return response()->json($car);
    }

    /**
     * @param int $id
     * @return JsonResponse
     */
    public function buy(int $id)
    {
        $car = Car::find($id);

        if (!$car) {
            return response()->json([
                'errors' => [
                    "Car does not exist"
                ]
            ], 404);
        }

        $profile = Profile::find(Auth::user()->id);

        $is_car_in_profile = !!$profile->cars->find($id);

        if ($is_car_in_profile) {
            return response()->json([
                'errors' => [
                    'The car is already bought'
                ]
            ], 400);
        }

        if ($profile->money < $car->price) {
            return response()->json([
                'errors' => [
                    'Not enough money to buy the car'
                ]
            ]);
        }

        $profile->money -= $car->price;
        $profile->save();

        $profile->cars()->attach($id);

        return response()->json([
            'message' => 'The car was successfully bought'
        ], 201);
    }

    /**
     * @return JsonResponse
     */
    public function sell(int $id)
    {
        if (!Car::find($id)) {
            return response()->json([
                'errors' => [
                    "Car does not exist"
                ]
            ], 404);
        }

        $profile = Profile::find(Auth::user()->id);

        $car = $profile->cars->find($id);

        if (!$car) {
            return response()->json([
                'errors' => [
                    'This car has not been bought'
                ]
            ], 400);
        }

        $profile->money += $car->price;
        $profile->save();

        $profile->cars()->detach($id);

        return response()->json(null, 204);
    }
}
