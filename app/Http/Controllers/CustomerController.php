<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index', [
            'customers' => Customer::all()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        $store = Customer::create($validated);

        if ($store){
            return redirect()->route('customers.index')->with('success', 'Customer created successfully');
        }else{
            return redirect()->route('customers.index')->with('error', 'Customer failed to create');
        }
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'email' => 'required',
            'address' => 'required',
        ]);

        $search = Customer::find($request->id);
        if (!$search){
            return redirect()->route('customers.index')->with('error', 'Customer not found');
        }

        $update = Customer::where('id', $request->id)->update($validated);

        if ($update){
            return redirect()->route('customers.index')->with('success', 'Customer updated successfully');
        }else{
            return redirect()->route('customers.index')->with('error', 'Customer failed to update');
        }
    }

    public function getDetails(Customer $customer)
    {
        return response()->json($customer);
    }

    public function destroy(Request $request)
    {
        $find = Customer::find($request->id);
        if (!$find){
            return redirect()->route('customers.index')->with('error', 'Customer not found');
        }

        $destroy = Customer::destroy($request->id);

        if ($destroy){
            return redirect()->route('customers.index')->with('success', 'Customer deleted successfully');
        }else{
            return redirect()->route('customers.index')->with('error', 'Customer failed to delete');
        }
    }
}
