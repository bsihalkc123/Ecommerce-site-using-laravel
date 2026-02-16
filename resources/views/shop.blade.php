-0 js-open-aside" data-aside="shopFilter">
                <svg class="d-inline-block align-middle me-2" width="14" height="10" viewBox="0 0 14 10" fill="none"
                  xmlns="http://www.w3.org/2000/svg">
                  <use href="#icon_filter" />
                </svg>
                <span class="text-uppercase fw-medium d-inline-block align-middle">Filter</span>
              </button>
            </div>
          </div>
        </div>

        <div class="products-grid row row-cols-2 row-cols-md-3" id="products-grid">
            @foreach ($products as $product)
          <div class="product-card-wrapper">
            <div class="product-card mb-3 mb-md-4 mb-xxl-5">
              <div class="pc__img-wrapper">
                <div class="swiper-container background-img js-swiper-slider" data-settings='{"resizeObserver": true}'>
                  <div class="swiper-wrapper">
                    <div class="swiper-slide">
                      <a href="{{route('shop.product.details',['product_slug'=>$product->slug])}}"><img loading="lazy" src="{{asset('uploads/products/')}}/{{$product->image}}" width="330" height="400" alt="{{$product->name}}" class="pc__img"></a>
                    </div>
                    <div class="swiper-slide">
                        @foreach(explode(',', $product->images) as $gimg)
                      <a href="{{route('shop.product.details',['product_slug'=>$product->slug])}}"><img loading="lazy" src="{{asset('uploads/products/')}}/{{$gimg}}" width="330" height="400" alt="{{$product->name}}" class="pc__img"></a>
                        @endforeach
                    </div>
                  </div>
                  <span class="pc__img-prev"><svg width="7" height="11" viewBox="0 0 7 11"
                      xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_prev_sm" />
                    </svg></span>
                  <span class="pc__img-next"><svg width="7" height="11" viewBox="0 0 7 11"
                      xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_next_sm" />
                    </svg></span>
                </div>
                @if(Cart::instance('cart')->content()->where('id', $product->id)->count() > 0)
                  <a href="{{ route('cart.index') }}" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium btn-warning mb-3">Go to Cart</a>
                @else
                <form name="addtocart-form" method="post" action="{{route('cart.add')}}">
                  @csrf
                  <input type="hidden" name="id" value="{{ $product->id }}">
                  <input type="hidden" name="quantity" value="1">
                  <input type="hidden" name="name" value="{{ $product->name }}">
                  <input type="hidden" name="price" value="{{ $product->sale_price== '' ? $product->regular_price : $product->sale_price }}"/>
                <button type="submit" class="pc__atc btn anim_appear-bottom btn position-absolute border-0 text-uppercase fw-medium" data-aside="cartDrawer" title="Add To Cart">Add To Cart</button>
                </form>
                @endif
              </div>
              <div class="pc__info position-relative">
                <p class="pc__category">{{ $product->category->name }}</p>
                <h6 class="pc__title"><a href="{{route('shop.product.details',['product_slug'=>$product->slug])}}">{{ $product->name }}</a></h6>
                <div class="product-card__price d-flex">
                  <span class="money price">
                    @if($product->saleprice)
                        <s>${{$product->regular_price}}</s> $ {{ $product->saleprice }}
                    @else
                      ${{ $product->regular_price }}
                    @endif
                  </span>
                </div>
                <div class="product-card__review d-flex align-items-center">
                  <div class="reviews-group d-flex">
                    <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_star" />
                    </svg>
                    <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_star" />
                    </svg>
                    <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_star" />
                    </svg>
                    <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_star" />
                    </svg>
                    <svg class="review-star" viewBox="0 0 9 9" xmlns="http://www.w3.org/2000/svg">
                      <use href="#icon_star" />
                    </svg>
                  </div>
                  <span class="reviews-note text-lowercase text-secondary ms-1">8k+ reviews</span>
                </div>

                <button class="pc__btn-wl position-absolute top-0 end-0 bg-transparent border-0 js-add-wishlist"
                  title="Add To Wishlist">
                  <svg width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <use href="#icon_heart" />
                  </svg>
                </button>
              </div>
            </div>
          </div>
          @endforeach
        </div>
        
        <div class="divider"></div>
        <div class="flex items-center justify-between flex wrap gap-10 wgp-pagination">
            {{ $products->withQueryString()->links('pagination::bootstrap-5') }}
      </div>
    </section>
  </main>
  <form id="frmfilter" method="GET" action="{{route('shop.index')}}">
    <input type="hidden" name="page" value="{{$products->currentPage()}}"/>
    <input type="hidden" name="size" id="size" value="{{$size}}"/>
    <input type="hidden" name="order" id="order" value="{{$order}}"/>
  </form>
@endsection

@push('scripts')
<script>
  $(function() {
    $('#page-size').on('change', function() {
      $('#size').val($('#page-size option:selected').val());
      $('#frmfilter').submit();
    })
    $('#orderby').on('change', function() {
      $('#order').val($('#orderby option:selected').val());
      $('#frmfilter').submit();
    })
  });
</script>
@endpush