<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Supplier;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(): Response
    {
        return Inertia::render('Dashboard', [
            'stats' => [
                'suppliers' => Supplier::count(),
                'products' => Product::count(),
                'orders' => Order::count(),
                'openOrders' => Order::where('status', 'open')->count(),
            ],
        ]);
    }
}
