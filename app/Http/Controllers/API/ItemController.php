<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Item;
use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = Item::with('category')->get();
        return response()->json($items);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:0',
            'image_url' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ]);

        $item = Item::create($request->all());

        return response()->json($item, 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Item $item)
    {
        return response()->json($item->load('category'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Item $item)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'sometimes|required|numeric|min:0',
            'quantity' => 'sometimes|required|integer|min:0',
            'image_url' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
        ]);

        $item->update($request->all());

        return response()->json($item);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Item $item)
    {
        $item->delete();

        return response()->json(null, 204);
    }

    /**
     * Search for items.
     */
    public function search(Request $request)
    {
        $query = $request->get('query');
        
        $items = Item::where('name', 'ILIKE', "%{$query}%")
            ->orWhere('description', 'ILIKE', "%{$query}%")
            ->with('category')
            ->get();
            
        return response()->json($items);
    }
}