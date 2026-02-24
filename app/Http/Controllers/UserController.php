<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Container\Attributes\Auth as AttributesAuth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Surfsidemedia\Shoppingcart\Cart;

class UserController extends Controller
{
    public function index()
    {
     return view('user.index');  // Logic to list users
    }
    
    public function orders()
    {
        $orders = Order::where('user_id', Auth::user()->id)->orderBy('created_at', 'desc')->paginate(10);
        return view('user.orders', compact('orders'));
    }
    public function order_details($order_id)
    {
        $order = Order::where('user_id', Auth::user()->id)->where('id', $order_id)->first();
        if($order)
        {
            $orderItems = OrderItem::where('order_id', $order->id)->orderBy('id')->paginate(12); 
            $transaction = Transaction::where('order_id', $order->id)->first();
            return view('user.order_details', compact('order', 'orderItems', 'transaction'));
        }
        else
        {
            return redirect()->route('login');
        }

    }
    public function cancel_order(Request $request)
    {
        $order = Order::find($request->order_id);
        $order->status = 'canceled';
        $order->canceled_date = Carbon::now();
        $order->save();
        return back()->with('status', 'Order canceled successfully.');
    }
}
