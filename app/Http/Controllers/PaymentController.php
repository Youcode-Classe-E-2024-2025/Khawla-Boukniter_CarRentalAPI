<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Stripe\Stripe;
use Stripe\PaymentIntent;

/**
 * @OA\Tag(
 *     name="Payments",
 *     description="API Endpoints for payment management"
 * )
 */
class PaymentController extends Controller implements HasMiddleware
{
    public static function middleware()
    {
        return [
            new Middleware('auth:sanctum', except: ['index', 'show'])
        ];
    }

    /**
     * @OA\Get(
     *     path="/api/payments",
     *     tags={"Payments"},
     *     summary="Get list of payments",
     *     @OA\Parameter(
     *         name="rental_id",
     *         in="query",
     *         description="Filter by rental"
     *     ),
     *     @OA\Parameter(
     *         name="min_amount",
     *         in="query",
     *         description="Filter by min amount"
     *     ),
     *     @OA\Parameter(
     *         name="max_amount",
     *         in="query",
     *         description="Filter by max amount"
     *     ),
     *     @OA\Parameter(
     *         name="payment_date",
     *         in="query",
     *         description="Filter by payment date"
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by status"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of payments"
     *     )
     * )
     */
    public function index(Request $request)
    {
        $query = Payment::query();

        if ($request->has('rental_id')) {
            $query->where('rental_id', $request->rental_id);
        }

        if ($request->has('min_amount')) {
            $query->where('amount', '>=' . $request->min_amount);
        }

        if ($request->has('max_amount')) {
            $query->where('amount', '<=' . $request->max_amount);
        }

        if ($request->has('payment_date')) {
            $query->where('payment_date', $request->payment_date);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return $query->paginate(10);
    }

    /**
     * @OA\Post(
     *     path="/api/payments",
     *     tags={"Payments"},
     *     summary="Create a new payment",
     *     security={{ "bearerAuth": {} }},
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"rental_id","amount","payment_date"},
     *             @OA\Property(property="rental_id", type="integer"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="payment_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Payment created"
     *     )
     * )
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date|date_format:Y-m-d',
        ]);

        Stripe::setApiKey(config('stripe.secret'));

        try {
            $paymentIntent = PaymentIntent::create([
                'amount' => $validated['amount'] * 100,
                'currency' => 'MAD',
                'metadate' => ['rental_id' => $validated['rental_id']]
            ]);

            $validated['status'] = 'pending';
            $validated['stripe_payment_id'] = $paymentIntent->id;

            $payment = $request->user()->payments()->create($validated);

            return response()->json(['payment' => $payment, 'clientSecret' => $paymentIntent->client_secret], 201);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/payments/{id}",
     *     tags={"Payments"},
     *     summary="Get payment details",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment details"
     *     )
     * )
     */
    public function show(Payment $payment)
    {
        return response()->json($payment, 200);
    }

    /**
     * @OA\Put(
     *     path="/api/payments/{id}",
     *     tags={"Payments"},
     *     summary="Update payment details",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID"
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             required={"rental_id","amount","payment_date"},
     *             @OA\Property(property="rental_id", type="integer"),
     *             @OA\Property(property="amount", type="number"),
     *             @OA\Property(property="payment_date", type="string", format="date")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment updated"
     *     )
     * )
     */
    public function update(Request $request, Payment $payment)
    {
        $validated = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date|date_format:Y-m-d',
        ]);

        $payment->update($validated);

        return response()->json($payment, 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/payments/{id}",
     *     tags={"Payments"},
     *     summary="Delete a payment",
     *     security={{ "bearerAuth": {} }},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         description="Payment ID"
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Payment deleted"
     *     )
     * )
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json(['message' => 'payment deleted'], 200);
    }
}
