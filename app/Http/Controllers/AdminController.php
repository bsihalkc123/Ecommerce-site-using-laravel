<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use Carbon\Carbon;
use App\Models\Coupon;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index()
    {
            return view('admin.index');  // Logic to list admins
    } 
    public function brands()
    {
        $brands = Brand::orderBy('id','Desc')->paginate(10); 
        return view('admin.brands', compact('brands')); // Logic to list brands
    }
    public function add_brand(){
        return view('admin.brand-add');
    }
    public function brand_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = new Brand();
        $brand->name = $request->name;
        $brand->slug = str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.".".$file_extension;
        $this->GenerateBrandThumbnailsImage($image, $file_name);
        $brand->image = $file_name;
        $brand->save();
        return redirect()->route('admin.brands')->with('status', 'Brand created successfully.');
    }
    public function brand_edit($id){
        $brand = Brand::find($id);
        return view('admin.brand-edit', compact('brand'));
    }
    public function brand_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:brands,slug,'.$request->id,
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $brand = Brand::find($request->id);
        $brand->name = $request->name;
        $brand->slug = str::slug($request->name);
        if($request->hasFile('image')){
            // Delete old image
            $old_image_path = public_path('uploads/brands/'.$brand->image);
            if(File::exists($old_image_path)){
                File::delete($old_image_path);
            }
            // Upload new image
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.".".$file_extension;
            $this->GenerateBrandThumbnailsImage($image, $file_name);
            $brand->image = $file_name;
        }
        $brand->update();
        return redirect()->route('admin.brands')->with('status', 'Brand updated successfully.');
    }

    public function GenerateBrandThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/brands');
        $img = Image::read($image->Path());  
        $img->cover(124,124,"top");
        $img->resize(124,124,function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$imageName);
        }
    public function brand_delete($id){
        $brand = Brand::find($id);
        // Delete image file
        $image_path = public_path('uploads/brands/'.$brand->image);
        if(File::exists($image_path)){
            File::delete($image_path);
        }
        $brand->delete();
        return redirect()->route('admin.brands')->with('status', 'Brand deleted successfully.');
    }    
    public function categories(){
        $categories = Category::orderBy('id','Desc')->paginate(10);
        Return view('admin.categories', compact('categories'));
    } 
    public function category_add(){
        return view('admin.category-add');
    }
    public function category_store(Request $request){   
         $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:categories,slug',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $category = new Category();
        $category->name = $request->name;
        $category->slug = str::slug($request->name);
        $image = $request->file('image');
        $file_extension = $request->file('image')->extension();
        $file_name = Carbon::now()->timestamp.".".$file_extension;
        $this->GenerateCategoryThumbnailsImage($image, $file_name);
        $category->image = $file_name;
        $category->save();
        return redirect()->route('admin.categories')->with('status', 'Category created successfully.');
    }
    public function category_edit($id){
        $category = Category::find($id);
        return view('admin.category-edit', compact('category'));
    }
    public function category_update(Request $request){
    $request->validate([
        'name' => 'required',
        'slug' => 'required|unique:categories,slug,'.$request->id,
        'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
    ]);

        $category = Category::find($request->id);
        $category->name = $request->name;
        $category->slug = str::slug($request->name);
        if($request->hasFile('image')){
            // Delete old image
            $old_image_path = public_path('uploads/categories/'.$category->image);
            if(File::exists($old_image_path)){
                File::delete($old_image_path);
            }
            // Upload new image
            $image = $request->file('image');
            $file_extension = $request->file('image')->extension();
            $file_name = Carbon::now()->timestamp.".".$file_extension;
            $this->GenerateCategoryThumbnailsImage($image, $file_name);
            $category->image = $file_name;
        }
        $category->update();
        return redirect()->route('admin.categories')->with('status', 'Category updated successfully.');
    }
    public function GenerateCategoryThumbnailsImage($image, $imageName){
        $destinationPath = public_path('uploads/categories');
        $img = Image::read($image->Path());  
        $img->cover(124,124,"top");
        $img->resize(124,124,function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$imageName);
    }
    public function category_delete($id){
        $category = category::find($id);
        // Delete image file
        $image_path = public_path('uploads/categories/'.$category->image);
        if(File::exists($image_path)){
            File::delete($image_path);
        }
        $category->delete();
        return redirect()->route('admin.categories')->with('status', 'Category deleted successfully.');
    }
    public function products(){
        $products = Product::orderBy('created_at','Desc')->paginate(10);
        return view('admin.products', compact('products'));
    }    
    public function product_add(){
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-add', compact('categories', 'brands'));
    }
    public function product_store(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug',
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'quantity' => 'required',
            'image' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
            'featured' => 'required|in:0,1',

        ]);
        $product = new Product();
        $product->name = $request->name;
        $product->slug = str::slug($request->name);
        $product->short_description = $request->short_description;      
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->quantity = $request->quantity;    
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->featured = $request->featured;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            $image = $request->file('image');
            $imageName =$current_timestamp.".".$image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }
        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images')){
            $allowedfileExtion= ['jpg','png','jpeg','gif','svg'];
            $files = $request->file('images');
            foreach($files as $file){
                $extension = $file->getClientOriginalExtension();
                $gcheck = in_array($extension,$allowedfileExtion);
                if($gcheck){
                    $gfilename = $current_timestamp."-".$counter.".".$extension;
                    $this->GenerateProductThumbnailImage($file, $gfilename);
                    array_push($gallery_arr, $gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(", ", $gallery_arr);
        }
        $product->images = $gallery_images;
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product created successfully.');
    }

    public function GenerateProductThumbnailImage($image, $imageName){   
        $destinationPathThumbnail = public_path('uploads/products/thumbnails');
        $destinationPath = public_path('uploads/products');
        $img = Image::read($image->Path());  
        $img->cover(540,689,"top");
        $img->resize(104,104,function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPathThumbnail.'/'.$imageName);
        $img->resize(540,689,function ($constraint) {
            $constraint->aspectRatio();
        })->save($destinationPath.'/'.$imageName);    
    }
    public function product_edit($id){
        $product = Product::find($id);
        $categories = Category::select('id','name')->orderBy('name')->get();
        $brands = Brand::select('id','name')->orderBy('name')->get();
        return view('admin.product-edit', compact('product', 'categories', 'brands'));
    }
    public function product_update(Request $request){
        $request->validate([
            'name' => 'required',
            'slug' => 'required|unique:products,slug,'.$request->id,
            'short_description' => 'required',
            'description' => 'required',
            'regular_price' => 'required',
            'sale_price' => 'required',
            'SKU' => 'required',
            'stock_status' => 'required|in:in_stock,out_of_stock',
            'quantity' => 'required',
            'image' => 'mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required',
            'brand_id' => 'required',
            'featured' => 'required|in:0,1',

        ]);
        $product = Product::find($request->id);
        $product->name = $request->name;
        $product->slug = str::slug($request->name);
        $product->short_description = $request->short_description;      
        $product->description = $request->description;
        $product->regular_price = $request->regular_price;
        $product->sale_price = $request->sale_price;
        $product->SKU = $request->SKU;
        $product->stock_status = $request->stock_status;
        $product->quantity = $request->quantity;    
        $product->category_id = $request->category_id;
        $product->brand_id = $request->brand_id;
        $product->featured = $request->featured;

        $current_timestamp = Carbon::now()->timestamp;

        if($request->hasFile('image')){
            if(File::exists(public_path('uploads/products/'.$product->image))){
                File::delete(public_path('uploads/products/'.$product->image));
            }
            if(File::exists(public_path('uploads/products/thumbnails/'.$product->image))){
                File::delete(public_path('uploads/products/thumbnails/'.$product->image));
            }
            $image = $request->file('image');
            $imageName =$current_timestamp.".".$image->extension();
            $this->GenerateProductThumbnailImage($image, $imageName);
            $product->image = $imageName;
        }
        $gallery_arr = array();
        $gallery_images = "";
        $counter = 1;
        if($request->hasFile('images')){
            foreach(explode(", ", $product->images) as $ofile){
                if(File::exists(public_path('uploads/products/'.$ofile))){
                    File::delete(public_path('uploads/products/'.$ofile));
                }
                if(File::exists(public_path('uploads/products/thumbnails/'.$ofile))){
                    File::delete(public_path('uploads/products/thumbnails/'.$ofile));
                }
            }
            $allowedfileExtion= ['jpg','png','jpeg','gif','svg'];
            $files = $request->file('images');
            foreach($files as $file){
                $extension = $file->getClientOriginalExtension();
                $gcheck = in_array($extension,$allowedfileExtion);
                if($gcheck){
                    $gfilename = $current_timestamp."-".$counter.".".$extension;
                    $this->GenerateProductThumbnailImage($file, $gfilename);
                    array_push($gallery_arr, $gfilename);
                    $counter = $counter + 1;
                }
            }
            $gallery_images = implode(", ", $gallery_arr);
            $product->images = $gallery_images;
        }
        $product->save();
        return redirect()->route('admin.products')->with('status', 'Product updated successfully.');
    }
    public function product_delete($id){
        $product = Product::find($id);
        // Delete image file
        if(File::exists(public_path('uploads/products/'.$product->image))){
            File::delete(public_path('uploads/products/'.$product->image));
        }
        if(File::exists(public_path('uploads/products/thumbnails/'.$product->image))){
            File::delete(public_path('uploads/products/thumbnails/'.$product->image));
        }
        foreach(explode(", ", $product->images) as $ofile){
            if(File::exists(public_path('uploads/products/'.$ofile))){
                File::delete(public_path('uploads/products/'.$ofile));
            }
            if(File::exists(public_path('uploads/products/thumbnails/'.$ofile))){
                File::delete(public_path('uploads/products/thumbnails/'.$ofile));
            }
        }
        $product->delete();
        return redirect()->route('admin.products')->with('status', 'Product deleted successfully.');
    }
    public function product_view($id){
        $product = Product::with(['category', 'brand'])->findOrFail($id);
        return view('admin.product-view', compact('product'));
    }
    public function coupons(){
        $coupons = Coupon::orderBy('expiry_date','Desc')->paginate(12);
        return view('admin.coupons', compact('coupons'));
    }
}