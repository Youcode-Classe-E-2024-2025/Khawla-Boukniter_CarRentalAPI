<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class PaymentController extends Controller implements HasMiddleware
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
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rental_id' => 'required|exists:rentals,id',
            'amount' => 'required|numeric|min:0',
            'payment_date' => 'required|date|date_format:Y-m-d',
        ]);

        $payment = $request->user()->payments()->create($validated);

        return response()->json($payment, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return response()->json($payment, 200);
    }

    /**
     * Update the specified resource in storage.
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
     * Remove the specified resource from storage.
     */
    public function destroy(Payment $payment)
    {
        $payment->delete();

        return response()->json(['message' => 'payment deleted'], 200);
    }
}
