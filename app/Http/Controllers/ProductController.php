<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', [
            'products' => Product::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|integer',
            'description' => 'required'
        ]);

        $store = Product::create($validated);

        if ($store){
            return redirect()->route('products.index')->with('success', 'Product created successfully');
        }else{
            return redirect()->route('products.index')->with('error', 'Product failed to create');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'quantity' => 'required|integer',
            'price' => 'required|integer',
            'description' => 'required'
        ]);

        $search = Product::find($request->id);
        if (!$search){
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        $update = Product::where('id', $request->id)->update($validated);

        if ($update){
            return redirect()->route('products.index')->with('success', 'Product updated successfully');
        }else{
            return redirect()->route('products.index')->with('error', 'Product failed to update');
        }
    }

    public function getDetails(Product $product)
    {
        return response()->json($product);
    }

    public function destroy(Request $request)
    {
        $find = Product::find($request->id);
        if (!$find){
            return redirect()->route('products.index')->with('error', 'Product not found');
        }

        $destroy = Product::destroy($request->id);

        if ($destroy){
            return redirect()->route('products.index')->with('success', 'Product deleted successfully');
        }else{
            return redirect()->route('products.index')->with('error', 'Product failed to delete');
        }
    }
}
