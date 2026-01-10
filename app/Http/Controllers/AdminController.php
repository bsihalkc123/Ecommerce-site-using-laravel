<?php

namespace App\Http\Controllers;
use App\Models\Brand;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Intervention\Image\Laravel\Facades\Image;
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
    $this->GenerateBrandThumbailsImage($image, $file_name);
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
        $this->GenerateBrandThumbailsImage($image, $file_name);
        $brand->image = $file_name;
    }
    $brand->update();
    return redirect()->route('admin.brands')->with('status', 'Brand updated successfully.');
}

public function GenerateBrandThumbailsImage($image, $imageName){
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
}
