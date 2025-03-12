<?php

namespace App\Http\Controllers;

use App\Models\Rental;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Gate;

/**
 * @OA\Tag(
 *     name="Rentals",
 *     description="API Endpoints for rental management"
 * )
 */

class RentalController extends Controller implements HasMiddleware
{

    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/rentals",
     *     tags={"Rentals"},
     *     summary="Get list of rentals",
     *     @OA\Parameter(
     *         name="user_id",
     *         in="query",
     *         description="Filter by user"
     *     ),
     *     @OA\Parameter(
     *         name="car_id",
     *         in="query",
     *         description="Filter by car"
     *     ),
     *     @OA\Parameter(
     *         name="start_date",
     *         in="query",
     *         description="Filter by start date"
     *     ),
     *     @OA\Parameter(
     *         name="end_date",
     *         in="query",
     *         description="Filter by end date"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of rentals"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Rental::query();

        if ($request->has('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->has('car_id')) {
            $query->where('car_id', $request->car_id);
        }

        if ($request->has('start_date')) {
            $query->where('start_date', '>=' . $request->start_date);
        }

        if ($request->has('end_date')) {
            $query->where('end_date', '<=' . $request->end_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return $query->paginate(10);
    }

    /**
     * @OA\Post(
     *     path="/api/rentals",
     *     tags={"Rentals"},
     *     summary="Create a new rental",
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"user_id","car_id","start_date","end_date"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="car_id", type="integer"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Rental created"
     *     )
     * )
     */
    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'user_id' => 'required|exists:users,id',
                'car_id' => 'required|exists:cars,id',
                'start_date' => 'required|date|after_or_equal:today',
                'end_date' => 'required|date|after_or_equal:start_date'
            ]);

            $rental = $request->user()->rentals()->create($validated);

            return response()->json($rental, 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/rentals/{id}",
     *     tags={"Rentals"},
     *     summary="Get rental details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Rental ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental details"
     *     )
     * )
     */
    public function show(Rental $rental)
    {
        return response()->json($rental, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/rentals/{id}",
     *     tags={"Rentals"},
     *     summary="Update rental details",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Rental ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"user_id","car_id","start_date","end_date"},
     *             @OA\Property(property="user_id", type="integer"),
     *             @OA\Property(property="car_id", type="integer"),
     *             @OA\Property(property="start_date", type="string", format="date"),
     *             @OA\Property(property="end_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental updated"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/rentals/{id}",
     *     tags={"Rentals"},
     *     summary="Delete a rental",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Rental ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Rental deleted"
     *     )
     * )
     */
    public function destroy(Rental $rental)
    {

        Gate::authorize('modify', $rental);

        $rental->delete();

        return response()->json(['message' => 'rental deleted'], 200);
    }
}
