<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::all();

        return response()->json($cars);
    }

    public function buy($id)
    {
        $car = Car::find($id);

        // ????
    }
}
