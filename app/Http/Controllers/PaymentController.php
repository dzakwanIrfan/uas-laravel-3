<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index()
    {
        return view('payments.index', [
            'payments' => Payment::all()
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'method' => 'required',
            'status' => 'required',
        ]);

        $payment = Payment::where('id', $request->id)->update([
            'method' => $request->method,
        ]);

        $p = Payment::where('id', $request->id)->first();

        $order = Order::where('id', $p->order->id)->update([
            'status' => $request->status,
        ]);

        if ($payment && $order){
            return redirect()->route('payments.index')->with('success', 'Payment updated successfully');
        }else{
            return redirect()->route('payments.index')->with('error', 'Payment failed to update');
        }
    }

    public function getDetails(Payment $payment)
    {
        return response()->json($payment);
    }
}
