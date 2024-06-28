<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return view('orders.index', [
            'orders' => Order::all()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->product_id); 
        if ($request->quantity > $product->quantity){
            return redirect()->route('orders.index')->with('error', 'Product stock is not enough');
        }
        $total = $product->price * $request->quantity;

        $order = Order::create([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total' => $total,
            'status' => 'Pending'
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => '-',
        ]);

        if ($order && $payment){
            return redirect()->route('orders.index')->with('success', 'Order created successfully');
        }else{
            return redirect()->route('orders.index')->with('error', 'Order failed to create');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|integer',
        ]);

        $product = Product::findOrFail($request->product_id); 
        if ($request->quantity > $product->quantity){
            return redirect()->route('orders.index')->with('error', 'Product stock is not enough');
        }
        $total = $product->price * $request->quantity;

        $search = Order::find($request->id);
        if (!$search){
            return redirect()->route('orders.index')->with('error', 'Order not found');
        }

        $store = Order::where('id', $request->id)->update([
            'customer_id' => $request->customer_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'total' => $total,
            'status' => 'Pending'
        ]);

        if ($store){
            return redirect()->route('orders.index')->with('success', 'Order updated successfully');
        }else{
            return redirect()->route('orders.index')->with('error', 'Order failed to update');
        }
    }

    public function getDetails(Order $Order)
    {
        $product = Product::get();
        $customer = Customer::get();
        return response()->json([
            'order' => $Order,
            'product' => $product,
            'customer' => $customer
        ]);
    }

    public function details()
    {
        $product = Product::get();
        $customer = Customer::get();
        return response()->json([
            'products' => $product,
            'customers' => $customer
        ]);
    }

    public function edit($id)
    {
        $order = Order::findOrFail($id);
        return response()->json($order);
    }

    public function destroy(Request $request)
    {
        $find = Order::find($request->id);
        if (!$find){
            return redirect()->route('orders.index')->with('error', 'Order not found');
        }

        $destroy = Order::destroy($request->id);

        if ($destroy){
            return redirect()->route('orders.index')->with('success', 'Order deleted successfully');
        }else{
            return redirect()->route('orders.index')->with('error', 'Order failed to delete');
        }
    }
}
