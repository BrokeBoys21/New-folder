<?php
// app/Http/Controllers/CompanyController.php

namespace App\Http\Controllers;

use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class CompanyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    
    public function index()
    {
        $user = Auth::user();
        $company = Company::findOrFail($user->company_id);
        
        return response()->json([
            'status' => 'success',
            'company' => $company,
        ]);
    }
    
    public function show($id)
    {
        $user = Auth::user();
        
        // Only allow viewing the company if the user belongs to it
        if ($user->company_id != $id) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized to view this company',
            ], 403);
        }
        
        $company = Company::findOrFail($id);
        
        return response()->json([
            'status' => 'success',
            'company' => $company,
        ]);
    }
    
    public function update(Request $request)
    {
        $user = Auth::user();
        $company = Company::findOrFail($user->company_id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'address' => 'nullable|string|max:255',
            'phone' => 'nullable|string|max:20',
        ]);
        
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        
        $company->name = $request->name;
        $company->address = $request->address;
        $company->phone = $request->phone;
        $company->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Company updated successfully',
            'company' => $company,
        ]);
    }
    
    public function getCompanyUsers()
    {
        $user = Auth::user();
        $company = Company::with('users')->findOrFail($user->company_id);
        
        return response()->json([
            'status' => 'success',
            'users' => $company->users,
        ]);
    }
    
    public function getCompanyStatistics()
    {
        $user = Auth::user();
        $company = Company::findOrFail($user->company_id);
        
        $statistics = [
            'total_users' => $company->users()->count(),
            'total_deposits' => $company->deposits()->count(),
            'total_deposit_amount' => $company->deposits()->sum('amount'),
            'total_categories' => $company->categories()->count(),
            'total_items' => $company->items()->count(),
        ];
        
        return response()->json([
            'status' => 'success',
            'statistics' => $statistics,
        ]);
    }
}