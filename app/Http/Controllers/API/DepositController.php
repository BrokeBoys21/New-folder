<?php
// app/Http/Controllers/API/DepositController.php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class DepositController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    /**
     * Display a listing of the deposits.
     * Users can only see deposits from their company.
     */
    public function index()
    {
        $user = Auth::user();
        $deposits = Deposit::where('company_id', $user->company_id)->get();

        return response()->json([
            'status' => 'success',
            'deposits' => $deposits,
        ]);
    }

    /**
     * Store a newly created deposit.
     * Automatically assigns the user's company_id.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Auth::user();
        
        $deposit = Deposit::create([
            'amount' => $request->amount,
            'company_id' => $user->company_id,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Deposit created successfully',
            'deposit' => $deposit,
        ]);
    }

    /**
     * Display the specified deposit.
     * Users can only see deposits from their company.
     */
    public function show($id)
    {
        $user = Auth::user();
        $deposit = Deposit::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$deposit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deposit not found or unauthorized access',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'deposit' => $deposit,
        ]);
    }

    /**
     * Update the specified deposit.
     * Users can only update deposits from their company.
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }

        $user = Auth::user();
        $deposit = Deposit::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$deposit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deposit not found or unauthorized access',
            ], 404);
        }

        $deposit->amount = $request->amount;
        $deposit->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Deposit updated successfully',
            'deposit' => $deposit,
        ]);
    }

    /**
     * Remove the specified deposit.
     * Users can only delete deposits from their company.
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $deposit = Deposit::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$deposit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deposit not found or unauthorized access',
            ], 404);
        }

        $deposit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deposit deleted successfully',
        ]);
    }
}