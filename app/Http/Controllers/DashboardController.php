<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Plant;
use App\Models\Purchase;
use App\Models\Supplier;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('dashboard', [
            'totalPlants' => Plant::count(),
            'totalStock' => Plant::sum('stock'),
            'lowStockPlants' => Plant::where('stock', '<=', 5)->count(),
            'totalCategories' => Category::count(),
            'totalSuppliers' => Supplier::count(),
            'totalPurchases' => Purchase::count(),
            'recentPlants' => Plant::with('category')->latest()->limit(5)->get(),
            'recentPurchases' => Purchase::with(['supplier', 'items.plant'])
                ->latest('purchase_date')
                ->latest('id')
                ->limit(5)
                ->get(),
        ]);
    }
}
