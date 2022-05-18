<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Validation\Validator;

class CarController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|required',
            'price' => 'integer|required',
            'description' => 'string',
            'avatar' => 'string'
        ]);

        if ($validator->failed()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        Car::create([
            'name' => $request->name,
            'price' => $request->price,
            'description' => $request->description,
            'avatar' => $request->avatar
        ]);

        return response()->json([
            'message' => 'Successfully stored car'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $car = Car::find($id);

        return response()->json($car);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, $id)
    {
        Car::find($id)->update($request->all());

        return response()->json([
            'message' => 'Successfully updated car'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        Car::destroy($id);

        return response()->json([
            'message' => 'Successfully destroyed car'
        ]);
    }
}
