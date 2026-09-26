<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalOrders' => Order::count(),
            'paidOrders' => Order::where('status', 'paid')->count(),
            'inProgress' => Order::whereIn('status', ['pending', 'awaiting_delivery', 'processing', 'shipped'])->count(),
            'revenue' => Order::whereIn('status', ['paid', 'delivered', 'shipped', 'processing'])->sum('total'),
            'avgOrder' => round((float) Order::whereIn('status', ['paid', 'delivered', 'shipped', 'processing'])->avg('total'), 2),
            'products' => Product::count(),
            'buyers' => User::where('user_type', 'buyer')->count(),
            'admins' => User::whereIn('user_type', ['admin', 'staff'])->count(),
            'lowStock' => Product::where('stock', '<=', 5)->count(),
        ];

        $recentOrders = Order::with('user')->orderByDesc('created_at')->limit(7)->get();

        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as qty, SUM(line_total) as revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $recentCustomers = User::where('user_type', 'buyer')->orderByDesc('id')->limit(5)->get();

        $daily = Order::selectRaw('DATE(created_at) as d, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', Carbon::now()->subDays(14))
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts', 'recentCustomers', 'daily'));
    }
}
