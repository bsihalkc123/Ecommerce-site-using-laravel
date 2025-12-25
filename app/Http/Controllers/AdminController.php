<?php

namespace App\Http\Controllers;
use App\Models\Brand;

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
}
