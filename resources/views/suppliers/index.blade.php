@extends('layouts.app')

@section('title', 'Suppliers')

@section('content')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Suppliers</h1>
            <p class="page-subtitle mb-0">Keep your nursery supplier contacts organized.</p>
        </div>
        <a class="btn btn-success" href="{{ route('suppliers.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Supplier</a>
    </div>

    <section class="card">
        <div class="card-body border-bottom">
            <form class="row g-2 align-items-end" action="{{ route('suppliers.index') }}" method="GET">
                <div class="col-12 col-md-6">
                    <label class="form-label small fw-semibold" for="search">Search by supplier name</label>
                    <input class="form-control" id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="e.g. Green Valley Nursery">
                </div>
                <div class="col-auto d-flex gap-2">
                    <button class="btn btn-success" type="submit"><i class="bi bi-search me-1"></i>Search</button>
                    <a class="btn btn-outline-secondary" href="{{ route('suppliers.index') }}">Clear</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3 ps-lg-4">Supplier</th>
                        <th>Contact</th>
                        <th>Purchases</th>
                        <th class="text-end pe-3 pe-lg-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($suppliers as $supplier)
                        <tr>
                            <td class="ps-3 ps-lg-4 fw-semibold">{{ $supplier->name }}</td>
                            <td>{{ $supplier->contact ?: '—' }}</td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $supplier->purchases_count }}</span></td>
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('suppliers.edit', $supplier) }}" aria-label="Edit {{ $supplier->name }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                    <form action="{{ route('suppliers.destroy', $supplier) }}" method="POST" onsubmit="return confirm('Delete this supplier?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $supplier->name }}" title="Delete"><i class="bi bi-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <i class="bi bi-truck display-6 text-secondary"></i>
                                <p class="fw-semibold mt-2 mb-1">No suppliers found</p>
                                <p class="text-secondary small mb-3">Add your first supplier to start recording purchases.</p>
                                <a class="btn btn-success btn-sm" href="{{ route('suppliers.create') }}">Add a supplier</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($suppliers->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">{{ $suppliers->links() }}</div>
        @endif
    </section>
@endsection
