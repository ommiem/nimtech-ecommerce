<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\Product;

class PageController extends Controller
{
    public function show(Page $page)
    {
        if(!$page->published && !(auth()->check() && method_exists(auth()->user(),'isAdmin') && auth()->user()->isAdmin())){
            abort(404);
        }
        $page->load('category');

        $categoryProducts = collect();
        if ($page->category) {
            $categoryProducts = Product::query()
                ->where('category_id', $page->category->id)
                ->with('category')
                ->orderByDesc('id')
                ->take(12)
                ->get();
        }

        return view('theme::pages.show', compact('page', 'categoryProducts'));
    }
}
