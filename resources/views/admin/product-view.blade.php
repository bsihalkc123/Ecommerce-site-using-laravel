@extends('layouts.admin')

@section('content')
<div class="main-content-inner">
    <div class="main-content-wrap">
        <div class="flex items-center flex-wrap justify-between gap20 mb-27">
            <h3>View Product</h3>
            <ul class="breadcrumbs flex items-center flex-wrap justify-start gap10">
                <li>
                    <a href="{{ route('admin.index') }}">
                        <div class="text-tiny">Dashboard</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <a href="{{ route('admin.products') }}">
                        <div class="text-tiny">Products</div>
                    </a>
                </li>
                <li><i class="icon-chevron-right"></i></li>
                <li>
                    <div class="text-tiny">View Product</div>
                </li>
            </ul>
        </div>

        <div class="wg-box">
            <div class="cols gap22">

                <!-- Product Image -->
                <div class="col">
                    <div class="body-title mb-10">Product Image</div>
                    <img src="{{ asset('uploads/products/'.$product->image) }}"
                         style="max-width: 250px; border-radius: 10px;">
                </div>

                <!-- Product Info -->
                <div class="col">
                    <div class="body-title mb-10">Product Details</div>

                    <p><strong>Name:</strong> {{ $product->name }}</p>
                    <p><strong>Slug:</strong> {{ $product->slug }}</p>
                    <p><strong>SKU:</strong> {{ $product->SKU }}</p>

                    <p><strong>Category:</strong> {{ $product->category->name }}</p>
                    <p><strong>Brand:</strong> {{ $product->brand->name }}</p>

                    <p><strong>Regular Price:</strong> {{ $product->regular_price }}</p>
                    <p><strong>Sale Price:</strong> {{ $product->sale_price }}</p>

                    <p><strong>Stock Status:</strong>
                        <span class="badge {{ $product->stock_status == 'in_stock' ? 'bg-success' : 'bg-danger' }}">
                            {{ ucfirst(str_replace('_',' ', $product->stock_status)) }}
                        </span>
                    </p>

                    <p><strong>Quantity:</strong> {{ $product->quantity }}</p>

                    <p><strong>Featured:</strong>
                        {{ $product->featured ? 'Yes' : 'No' }}
                    </p>
                </div>
            </div>

            <div class="divider"></div>

            <!-- Descriptions -->
            <div>
                <div class="body-title mb-10">Short Description</div>
                <p>{{ $product->short_description }}</p>
            </div>

            <div class="divider"></div>

            <div>
                <div class="body-title mb-10">Description</div>
                <p>{{ $product->description }}</p>
            </div>

            <!-- Gallery -->
            @if($product->images)
            <div class="divider"></div>
            <div>
                <div class="body-title mb-10">Gallery Images</div>
                <div class="flex gap10 flex-wrap">
                    @foreach(explode(', ', $product->images) as $img)
                        <img src="{{ asset('uploads/products/'.$img) }}"
                             style="width:120px; border-radius:8px;">
                    @endforeach
                </div>
            </div>
            @endif

            <div class="divider"></div>

            <!-- Action Buttons -->
            <div class="flex gap10">
                <a href="{{ route('admin.product.edit', ['id'=>$product->id]) }}"
                   class="tf-button style-1">
                    <i class="icon-edit-3"></i> Edit
                </a>

                <a href="{{ route('admin.products') }}"
                   class="tf-button style-2">
                    Back
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
