<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

class RentalController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return Rental::paginate(10);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $rental = $request->user()->rentals()->create($validated);

        return response()->json($rental, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Rental $rental)
    {
        return response()->json($rental, 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Rental $rental)
    {
        Gate::authorize('modify', $rental);

        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'car_id' => 'required|exists:cars,id',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $rental->update($validated);

        return response()->json($rental, 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rental $rental)
    {

        Gate::authorize('modify', $rental);

        $rental->delete();

        return response()->json(['message' => 'rental deleted'], 200);
    }
}
