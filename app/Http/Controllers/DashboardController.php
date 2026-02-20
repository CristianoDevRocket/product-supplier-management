<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        $recentOrders = Order::with('supplier:id,name')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get(['id', 'supplier_id', 'order_date', 'status', 'total', 'created_at']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'suppliers' => Supplier::count(),
                'products' => Product::count(),
                'orders' => Order::count(),
                'openOrders' => Order::where('status', 'open')->count(),
                'processingOrders' => Order::where('status', 'processing')->count(),
                'totalRevenue' => Order::where('status', 'completed')->sum('total'),
            ],
            'recentOrders' => $recentOrders,
        ]);
    }
}
