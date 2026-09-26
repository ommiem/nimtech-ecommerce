<?php

namespace App\Http\Controllers;

use App\Models\Brand;

class BrandController extends Controller
{
    public function index()
    {
        $brands = Brand::where('active', true)->orderBy('name')->get();
        return view('theme::brands.index', compact('brands'));
    }
}
