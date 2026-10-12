@extends('layouts.app')

@section('title', 'Purchases')

@section('content')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Purchases</h1>
            <p class="page-subtitle mb-0">Record supplier orders and track received plant stock.</p>
        </div>
        <a class="btn btn-success" href="{{ route('purchases.create') }}"><i class="bi bi-plus-lg me-1"></i>Record Purchase</a>
    </div>

    <section class="card">
        <div class="card-body border-bottom">
            <form class="row g-2 align-items-end" action="{{ route('purchases.index') }}" method="GET">
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold" for="supplier_id">Supplier</label>
                    <select class="form-select" id="supplier_id" name="supplier_id">
                        <option value="">All suppliers</option>
                        @foreach ($suppliers as $supplier)
                            <option value="{{ $supplier->id }}" @selected((string) ($filters['supplier_id'] ?? '') === (string) $supplier->id)>{{ $supplier->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label small fw-semibold" for="status">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">All statuses</option>
                        @foreach (\App\Models\Purchase::STATUSES as $status)
                            <option value="{{ $status }}" @selected(($filters['status'] ?? '') === $status)>{{ ucfirst($status) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-success" type="submit"><i class="bi bi-funnel me-1"></i>Filter</button>
                    <a class="btn btn-outline-secondary" href="{{ route('purchases.index') }}">Clear</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3 ps-lg-4">Purchase</th>
                        <th>Supplier</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total Amount</th>
                        <th>Status</th>
                        <th class="text-end pe-3 pe-lg-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($purchases as $purchase)
                        <tr>
                            <td class="ps-3 ps-lg-4 fw-semibold">#{{ $purchase->id }}</td>
                            <td>{{ $purchase->supplier->name }}</td>
                            <td>{{ $purchase->purchase_date->format('M j, Y') }}</td>
                            <td>
                                <div>{{ $purchase->items->count() }} plants</div>
                                <div class="small text-secondary">{{ $purchase->items->pluck('plant.name')->filter()->take(2)->join(', ') }}{{ $purchase->items->count() > 2 ? ', …' : '' }}</div>
                            </td>
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
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('purchases.edit', $purchase) }}" aria-label="Edit purchase {{ $purchase->id }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('purchases.destroy', $purchase) }}" method="POST" onsubmit="return confirm('Delete this purchase record? Received plant quantities will be removed from stock.')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete purchase {{ $purchase->id }}" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <i class="bi bi-receipt display-6 text-secondary"></i>
                                <p class="fw-semibold mt-2 mb-1">No purchases found</p>
                                <p class="text-secondary small mb-3">Record a supplier purchase to track new stock.</p>
                                <a class="btn btn-success btn-sm" href="{{ route('purchases.create') }}">Record a purchase</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($purchases->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">{{ $purchases->links() }}</div>
        @endif
    </section>
@endsection
