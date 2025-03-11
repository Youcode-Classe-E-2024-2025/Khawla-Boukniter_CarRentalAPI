<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

/**
 * @OA\Tag(
 *     name="Cars",
 *     description="API Endpoints for car management"
 * )
 */
class CarController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/cars",
     *     tags={"Cars"},
     *     summary="Get list of cars",
     *     @OA\Parameter(
     *         name="make",
     *         in="query",
     *         description="Filter by car make"
     *     ),
     *     @OA\Parameter(
     *         name="model",
     *         in="query",
     *         description="Filter by car model"
     *     ),
     *     @OA\Parameter(
     *         name="year",
     *         in="query",
     *         description="Filter by year"
     *     ),
     *      @OA\Parameter(
     *         name="min_price",
     *         in="query",
     *         description="Filter by min price"
     *     ),
     *      @OA\Parameter(
     *         name="max_price",
     *         in="query",
     *         description="Filter by max price"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of cars"
     *     )
     * )
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
     * @OA\Post(
     *     path="/api/cars",
     *     tags={"Cars"},
     *     summary="Create a new car",
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"make","model","year","price"},
     *             @OA\Property(property="make", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Car created"
     *     )
     * )
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
     * @OA\Get(
     *     path="/api/cars/{id}",
     *     tags={"Cars"},
     *     summary="Get car details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Car ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car details"
     *     )
     * )
     */
    public function show(Car $car)
    {
        return response()->json($car, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/cars/{id}",
     *     tags={"Cars"},
     *     summary="Update car details",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Car ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"make","model","year","price"},
     *             @OA\Property(property="make", type="string"),
     *             @OA\Property(property="model", type="string"),
     *             @OA\Property(property="year", type="integer"),
     *             @OA\Property(property="price", type="number")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car updated"
     *     )
     * )
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
     * @OA\Delete(
     *     path="/api/cars/{id}",
     *     tags={"Cars"},
     *     summary="Delete a car",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Car ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Car deleted"
     *     )
     * )
     */
    public function destroy(Car $car)
    {
        $car->delete();

        return response()->json(['message' => 'car deleted'], 200);
    }
}
