<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;

class CarController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Car::query();

        if ($request->has('make')) {
            $query->where('make', '%' . $request->make . '%');
        }

        if ($request->has('model')) {
            $query->where('model', '%' . $request->model . '%');
        }

        if ($request->has('year')) {
            $query->where('year', '%' . $request->year . '%');
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=' . $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=' . $request->max_price);
        }

        return $query->paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|digits:4',
            'price' => 'required|numeric|min:0'
        ]);

        $car = Car::create($validated);

        return response()->json($car, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Car $car)
    {
        return response()->json($car, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Car $car)
    {
        $validated = $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'required|integer|digits:4',
            'price' => 'required|numeric|min:0'
        ]);

        $car->update($validated);

        return response()->json($car, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return response()->json(['message' => 'car deleted'], 200);
    }
}
