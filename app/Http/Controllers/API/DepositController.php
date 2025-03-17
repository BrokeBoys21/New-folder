<?php


namespace App\Http\Controllers;

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

    public function index()
    {
        $user = Auth::user();
        $deposits = Deposit::where('company_id', $user->company_id)->get();

        return response()->json([
            'status' => 'success',
            'deposits' => $deposits,
        ]);
    }

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

    public function show($id)
    {
        $user = Auth::user();
        $deposit = Deposit::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$deposit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deposit not found',
            ], 404);
        }

        return response()->json([
            'status' => 'success',
            'deposit' => $deposit,
        ]);
    }

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
                'message' => 'Deposit not found',
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

    public function destroy($id)
    {
        $user = Auth::user();
        $deposit = Deposit::where('id', $id)
            ->where('company_id', $user->company_id)
            ->first();

        if (!$deposit) {
            return response()->json([
                'status' => 'error',
                'message' => 'Deposit not found',
            ], 404);
        }

        $deposit->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Deposit deleted successfully',
        ]);
    }
}