<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $to = Carbon::now();
        $from = Carbon::parse($request->query('from', Carbon::now()->subDays(30)->toDateString()))->startOfDay();
        $to = Carbon::parse($request->query('to', $to->toDateString()))->endOfDay();

        $ordersQuery = Order::whereBetween('created_at', [$from, $to]);

        $summary = [
            'orders' => (clone $ordersQuery)->count(),
            'revenue' => (clone $ordersQuery)->whereIn('status', ['paid', 'delivered', 'shipped', 'processing'])->sum('total'),
            'avg' => round((float) (clone $ordersQuery)->whereIn('status', ['paid', 'delivered', 'shipped', 'processing'])->avg('total'), 2),
        ];

        $byStatus = (clone $ordersQuery)
            ->select('status', DB::raw('COUNT(*) as orders'), DB::raw('SUM(total) as revenue'))
            ->groupBy('status')
            ->orderByDesc('orders')
            ->get();

        $daily = (clone $ordersQuery)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('d')
            ->orderBy('d')
            ->get();

        $topProducts = OrderItem::selectRaw('product_id, SUM(quantity) as qty, SUM(line_total) as revenue')
            ->with('product')
            ->groupBy('product_id')
            ->orderByDesc('qty')
            ->limit(5)
            ->get();

        $topCustomers = (clone $ordersQuery)
            ->selectRaw('COALESCE(user_id, 0) as uid, full_name, email, COUNT(*) as orders, SUM(total) as revenue')
            ->groupBy('uid', 'full_name', 'email')
            ->orderByDesc('revenue')
            ->limit(5)
            ->get();

        $lowStockThreshold = 5;
        $stockSummary = [
            'total' => Product::count(),
            'inStock' => Product::where('stock', '>', 0)->count(),
            'outOfStock' => Product::where('stock', '<=', 0)->count(),
            'lowStock' => Product::whereBetween('stock', [1, $lowStockThreshold])->count(),
            'inventoryValue' => (float) Product::where('stock', '>', 0)->selectRaw('SUM(stock * price) as total_value')->value('total_value'),
        ];

        $lowStockItems = Product::whereBetween('stock', [1, $lowStockThreshold])
            ->orderBy('stock')
            ->limit(15)
            ->get();

        $outOfStockItems = Product::where('stock', '<=', 0)
            ->orderByDesc('updated_at')
            ->limit(10)
            ->get();

        return view('admin.reports.index', compact(
            'from',
            'to',
            'summary',
            'byStatus',
            'daily',
            'topProducts',
            'topCustomers',
            'stockSummary',
            'lowStockItems',
            'outOfStockItems',
            'lowStockThreshold'
        ));
    }
}
