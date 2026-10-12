@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Dashboard</h1>
            <p class="page-subtitle mb-0">A quick overview of your nursery inventory.</p>
        </div>
        <a class="btn btn-success d-sm-none" href="{{ route('plants.create') }}">
            <i class="bi bi-plus-lg me-1"></i>Add a plant
        </a>
    </div>

    <div class="row g-3 g-xl-4 mb-4">
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small mb-1">Total Plants</div>
                        <div class="stat-value">{{ number_format($totalPlants) }}</div>
                        <div class="small text-secondary">plant varieties</div>
                    </div>
                    <span class="stat-icon text-success" style="background: #e9f5ed;"><i class="bi bi-flower2"></i></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small mb-1">Total Stock</div>
                        <div class="stat-value">{{ number_format($totalStock) }}</div>
                        <div class="small text-secondary">individual plants</div>
                    </div>
                    <span class="stat-icon text-primary" style="background: #eaf2fc;"><i class="bi bi-box-seam"></i></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small mb-1">Categories</div>
                        <div class="stat-value">{{ number_format($totalCategories) }}</div>
                        <div class="small text-secondary">plant groups</div>
                    </div>
                    <span class="stat-icon text-info" style="background: #e8f7f8;"><i class="bi bi-tags"></i></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small mb-1">Suppliers</div>
                        <div class="stat-value">{{ number_format($totalSuppliers) }}</div>
                        <div class="small text-secondary">supplier contacts</div>
                    </div>
                    <span class="stat-icon text-primary" style="background: #eaf2fc;"><i class="bi bi-truck"></i></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small mb-1">Purchases</div>
                        <div class="stat-value">{{ number_format($totalPurchases) }}</div>
                        <div class="small text-secondary">purchase records</div>
                    </div>
                    <span class="stat-icon text-success" style="background: #e9f5ed;"><i class="bi bi-receipt"></i></span>
                </div>
            </div>
        </div>
        <div class="col-12 col-sm-6 col-xl-4">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center justify-content-between">
                    <div>
                        <div class="text-secondary small mb-1">Low Stock</div>
                        <div class="stat-value">{{ number_format($lowStockPlants) }}</div>
                        <div class="small text-secondary">plants with 5 or fewer</div>
                    </div>
                    <span class="stat-icon text-warning-emphasis" style="background: #fff5df;"><i class="bi bi-exclamation-triangle"></i></span>
                </div>
            </div>
        </div>
    </div>

    <section class="card">
        <div class="card-header bg-white border-0 d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 px-lg-4 pt-4">
            <div>
                <h2 class="h5 fw-bold mb-1">Recent plants</h2>
                <p class="small text-secondary mb-0">The latest additions to your inventory.</p>
            </div>
            <a class="btn btn-outline-success btn-sm" href="{{ route('plants.index') }}">View all plants <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="card-body px-0 pt-3">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3 ps-lg-4">Plant</th>
                        <th>Category</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($recentPlants as $plant)
                        <tr>
                            <td class="ps-3 ps-lg-4">
                                <div class="fw-semibold">{{ $plant->plant_name }}</div>
                                <div class="small text-secondary">{{ $plant->scientific_name ?: 'Scientific name not added' }}</div>
                            </td>
                            <td>{{ $plant->category->name }}</td>
                            <td>{{ number_format($plant->quantity) }}</td>
                            <td>₱{{ number_format((float) $plant->price, 2) }}</td>
                            <td>
                                @if ($plant->quantity === 0)
                                    <span class="badge text-bg-secondary">Out of Stock</span>
                                @elseif ($plant->quantity <= 5)
                                    <span class="badge text-bg-warning">Low Stock</span>
                                @else
                                    <span class="badge text-bg-success">In Stock</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-5 text-center">
                                <i class="bi bi-flower1 display-6 text-success"></i>
                                <p class="fw-semibold mt-2 mb-1">Your inventory is ready to grow</p>
                                <p class="text-secondary small mb-3">Add your first plant to start tracking stock.</p>
                                <a class="btn btn-success btn-sm" href="{{ route('plants.create') }}">Add your first plant</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    <section class="card mt-4">
        <div class="card-header bg-white border-0 d-flex flex-wrap align-items-center justify-content-between gap-2 px-3 px-lg-4 pt-4">
            <div>
                <h2 class="h5 fw-bold mb-1">Recent purchases</h2>
                <p class="small text-secondary mb-0">Latest orders from your plant suppliers.</p>
            </div>
            <a class="btn btn-outline-success btn-sm" href="{{ route('purchases.index') }}">View purchases <i class="bi bi-arrow-right ms-1"></i></a>
        </div>
        <div class="card-body px-0 pt-3">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3 ps-lg-4">Purchase</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($recentPurchases as $purchase)
                        <tr>
                            <td class="ps-3 ps-lg-4 fw-semibold">#{{ $purchase->id }} <span class="small text-secondary fw-normal">({{ $purchase->items->count() }} items)</span></td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ $purchase->purchase_date->format('M j, Y') }}</td>
                            <td>₱{{ number_format((float) $purchase->total_amount, 2) }}</td>
                            <td>
                                @if ($purchase->status === \App\Models\Purchase::STATUS_RECEIVED)
                                    <span class="badge text-bg-success">Received</span>
                                @elseif ($purchase->status === \App\Models\Purchase::STATUS_CANCELLED)
                                    <span class="badge text-bg-secondary">Cancelled</span>
                                @else
                                    <span class="badge text-bg-warning">Pending</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-4 text-center text-secondary">No purchases recorded yet.</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </section>
@endsection
