<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Intervention\Image\Drivers\Gd\Modifiers\DrawEllipseModifier;
use Surfsidemedia\Shoppingcart\Facades\Cart;

class CartController extends Controller
{
    public function index()
    {
        $items = Cart::instance('cart')->content();    
        return view('cart', compact('items'));
    }
    public function add_to_cart(Request $request)
    {
        Cart::instance('cart')->add($request->id, $request->name, $request->quantity, $request->price)->associate('App\Models\Product');
        return redirect()->back();
    }
    public function increase_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty + 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }
    public function decrease_cart_quantity($rowId)
    {
        $product = Cart::instance('cart')->get($rowId);
        $qty = $product->qty - 1;
        Cart::instance('cart')->update($rowId, $qty);
        return redirect()->back();
    }
    public function remove_item($rowId)
    {
        Cart::instance('cart')->remove($rowId);
        return redirect()->back();
    }
    public function empty_cart()
    {
        Cart::instance('cart')->destroy();
        return redirect()->back();
    }
    public function apply_coupon_code(Request $request)
    {
        $coupon_code = trim($request->coupon_code);

        if($coupon_code){

            $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));

            $coupon = Coupon::where('code', $coupon_code)
                ->where('expiry_date', '>=', Carbon::today())
                ->where('cart_value', '<=', $cartSubtotal)
                ->first();

            if(!$coupon){
                return back()->with('error', 'Invalid coupon code!');
            }

            Session::put('coupon', [
                'code'=> $coupon->code,
                'type'=> $coupon->type,
                'value'=> $coupon->value,
                'cart_value'=> $coupon->cart_value
            ]);

            $this->calculateDiscount();

            return back()->with('success', 'Coupon code applied successfully!');
        }

        return back()->with('error', 'Invalid coupon code!');
    }
    public function calculateDiscount()
    {
        $discount = 0;

        if(Session::has('coupon')){

            $cartSubtotal = floatval(str_replace(',', '', Cart::instance('cart')->subtotal()));

            if(Session::get('coupon')['type'] == 'fixed'){
                $discount = Session::get('coupon')['value'];
            } else {
                $discount = ($cartSubtotal * Session::get('coupon')['value']) / 100;
            }

            $subtotalAfterDiscount = $cartSubtotal - $discount;
            $taxAfterDiscount = ($subtotalAfterDiscount * config('cart.tax')) / 100;
            $totalAfterDiscount = $subtotalAfterDiscount + $taxAfterDiscount;

            Session::put('discounts', [
                'discount' => number_format($discount,2,'.',''),
                'subtotal' => number_format($subtotalAfterDiscount,2,'.',''),
                'tax' => number_format($taxAfterDiscount,2,'.',''),
                'total' => number_format($totalAfterDiscount,2,'.','')
            ]);
        }
    }
    public function remove_coupon_code()
    {
        Session::forget('coupon');
        Session::forget('discounts');
        return back()->with('success', 'Coupon removed successfully!');
    }
    public function checkout()
    {
        if(!Auth::check()){
            return redirect()->route('login');
        }
        if(Session::has('coupon')){
            $this->calculateDiscount();
        }

        $address = Address::where('user_id', Auth::user()->id)
                    ->where('is_default', 1)
                    ->first();

        return view('checkout', compact('address'));
    }
    public function place_an_order(Request $request)
    {
        $userid = Auth::user()->id; 
        $address = Address::where('user_id', $userid)->where('is_default', true)->first();

        if(!$address){
            $request->validate([
                'name' => 'required|max:255',
                'phone' => 'required|numeric|digits:10',
                'zip' => 'required|numeric|digits:5',
                'city' => 'required',
                'state' => 'required',
                'address' => 'required',
                'locality' => 'required',
                'landmark' => 'required'
            ]);

                $address = new Address();
                $address->name = $request->name;
                $address->phone = $request->phone;
                $address->zip = $request->zip;
                $address->city = $request->city;
                $address->state = $request->state;
                $address->address = $request->address;
                $address->locality = $request->locality;
                $address->landmark = $request->landmark;
                $address->country = 'Nepal';
                $address->user_id = $userid;
                $address->is_default = true;
                $address->save();
        }
        $this->setAmountforCheckout();

        $order = new Order();
        $order->user_id = $userid;
<<<<<<< HEAD
        $order->subtotal = Session::get('checkout')['subtotal'];
        $order->discount = Session::get('checkout')['discount'];    
        $order->tax = Session::get('checkout')['tax'];
        $order->total = Session::get('checkout')['total'];
=======
        $order->subtotal = (float) str_replace(',', '', Session::get('checkout')['subtotal']);
        $order->discount = (float) str_replace(',', '', Session::get('checkout')['discount']);
        $order->tax = (float) str_replace(',', '', Session::get('checkout')['tax']);
        $order->total = (float) str_replace(',', '', Session::get('checkout')['total']);
>>>>>>> f37e4e8 (user and admin order status added)
        $order->name = $address->name;
        $order->phone = $address->phone;
        $order->locality = $address->locality;
        $order->address = $address->address;
        $order->city = $address->city;
        $order->state = $address->state;
        $order->country = $address->country;
        $order->landmark = $address->landmark;
        $order->zip = $address->zip;
        $order->save();

        foreach(Cart::instance('cart')->content() as $item){
            $orderItem = new OrderItem();
            $orderItem->order_id = $order->id;
            $orderItem->product_id = $item->model->id;
            $orderItem->quantity = $item->qty;
            $orderItem->price = $item->price;
            $orderItem->save();
        }

        if($request->mode == 'card'){
            //

        } elseif($request->mode == 'paypal') {
            //

        } elseif($request->mode == 'cod'){
        $transaction = new Transaction();
        $transaction->user_id = $userid;
        $transaction->order_id = $order->id;
        $transaction->mode = $request->mode;
        $transaction->status = 'pending';
        $transaction->save();

        }

        Cart::instance('cart')->destroy();
        Session::forget('checkout');
        Session::forget('coupon');
        Session::forget('discounts');
        Session::put('order_id', $order->id);
<<<<<<< HEAD
        return redirect()->route('cart.order.confirmation', compact('order'));
        
    }
    public function setAmountforCheckout(){
        if(!cart::instance('cart')->content()->count() > 0){
            Session::forget('checkout');
            return;
        }
        if(Session::has('coupon')){
            Session::put('checkout', [
                'discount' => Session::get('discounts')['discount'],
                'subtotal' => Session::get('discounts')['subtotal'],
                'tax' => Session::get('discounts')['tax'],
                'total' => Session::get('discounts')['total']
            ]);
        } else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => cart::instance('cart')->subtotal(),
                'tax' => cart::instance('cart')->tax(),
                'total' => cart::instance('cart')->total()
=======
        return redirect()->route('cart.order.confirmation');
        
    }
    public function setAmountforCheckout()
    {
        if(!Cart::instance('cart')->content()->count() > 0){
            Session::forget('checkout');
            return;
        }

        if(Session::has('coupon')){
            Session::put('checkout', [
                'discount' => (float) str_replace(',', '', Session::get('discounts')['discount']),
                'subtotal' => (float) str_replace(',', '', Session::get('discounts')['subtotal']),
                'tax' => (float) str_replace(',', '', Session::get('discounts')['tax']),
                'total' => (float) str_replace(',', '', Session::get('discounts')['total']),
            ]);
        } 
        else {
            Session::put('checkout', [
                'discount' => 0,
                'subtotal' => (float) str_replace(',', '', Cart::instance('cart')->subtotal()),
                'tax' => (float) str_replace(',', '', Cart::instance('cart')->tax()),
                'total' => (float) str_replace(',', '', Cart::instance('cart')->total()),
>>>>>>> f37e4e8 (user and admin order status added)
            ]);
        }
    }
    public function order_confirmation()
    {
<<<<<<< HEAD
        if(Session::has('order_id')){
            $order = Order::find(Session::get('order_id'));
            return view('order-confirmation', compact('order'));
        }
        return redirect()->route('cart.index');
=======
        $order_id = Session::get('order_id');

        if (!$order_id) {
            return redirect()->route('cart.checkout');
        }

        $order = Order::with('orderItems.product')->find($order_id);

        if (!$order) {
            return redirect()->route('cart.checkout');
        }

        return view('order-confirmation', compact('order'));
>>>>>>> f37e4e8 (user and admin order status added)
    }
}