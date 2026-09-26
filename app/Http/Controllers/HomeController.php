<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\Category;
use App\Models\Page;
use App\Models\Product;
use App\Models\Slide;

class HomeController extends Controller
{
    public function index()
    {
        $slides = Slide::active()->orderBy('sort_order')->get();
        $categories = Category::orderBy('name')->take(12)->get();
        $newProducts = Product::orderByDesc('id')->take(10)->get();
        $popularProducts = Product::withCount('orderItems')->orderByDesc('order_items_count')->take(10)->get();
        $settings = \App\Models\Setting::getCached();
        $dealsThreshold = (float) ($settings->deals_under_threshold ?? 5000);
        $budgetProducts = Product::where('price', '<', $dealsThreshold)->orderBy('price')->take(12)->get();
        $showDeals = $budgetProducts->count() >= 4;
        $latestPages = Page::with('category:id,name,slug')
            ->where('published', 1)
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();
        $campaigns = Campaign::query()
            ->where('published', true)
            ->withCount('products')
            ->orderByDesc('updated_at')
            ->take(3)
            ->get();

        return view('theme::home.index', compact('slides','categories','newProducts','popularProducts','budgetProducts','dealsThreshold','showDeals','settings','latestPages','campaigns'));
    }
}
