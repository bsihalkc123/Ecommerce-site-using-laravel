@extends('layouts.admin')
@section('content')

<style>
.wg-box {
    width: 100%;
    padding: 30px;
}

.table {
    width: 100%;
}

.table th,
.table td {
    text-align: center;
    vertical-align: middle;
}
</style>

<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">

            <!-- Page Header -->
            <div class="flex items-center flex-wrap justify-between gap20 mb-27">
                <h3>Orders</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">Orders</div>
                    </li>
                </ul>
            </div>

            <div class="wg-box">

                <!-- Search -->
                <div class="flex items-center justify-between gap10 flex-wrap">
                    <div class="wg-filter flex-grow">
                        <form class="form-search">
                            <fieldset class="name">
                                <input type="text" placeholder="Search here..." name="name">
                            </fieldset>
                            <div class="button-submit">
                                <button type="submit"><i class="icon-search"></i></button>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Orders Table -->
                <div class="wg-table table-all-user">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th style="width:70px">OrderNo</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Subtotal</th>
                                <th>Tax</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Order Date</th>
                                <th>Total Items</th>
                                <th>Delivered On</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($orders as $order)
                            <tr>
                                <td class="text-center">{{ $order->id }}</td>
                                <td class="text-center">{{ $order->name }}</td>
                                <td class="text-center">{{ $order->phone }}</td>
                                <td class="text-center">${{ $order->subtotal }}</td>
                                <td class="text-center">${{ $order->tax }}</td>
                                <td class="text-center">${{ $order->total }}</td>
                                <td class="text-center">
                                     @if ($order->status == 'delivered')
                                        <span class="badge bg-success">Delivered</span>
                                    @elseif ($order->status == 'canceled')
                                        <span class="badge bg-danger">Canceled</span>
                                    @else
                                        <span class="badge bg-warning">Ordered</span>
                                    @endif
                                </td>
                                <td class="text-center">{{ $order->created_at}}</td>
                                <td class="text-center">{{ $order->orderItems->count() }}</td>
                                <td class="text-center">{{ $order->delivered_date}}</td>
                                <td>
                                    <a href="{{ route('admin.order.details', ['order_id' => $order->id]) }}" class="tf-button style-1 w100">
                                        <i class="icon-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="11">No Orders Found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="divider"></div>
                <div class="flex items-center justify-between flex-wrap gap10 wgp-pagination">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="bottom-page">
        <div class="body-text">Copyright © 2024 SurfsideMedia</div>
    </div>
</div>

@endsection