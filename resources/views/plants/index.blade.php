@extends('layouts.app')

@section('title', 'Plants')

@section('content')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Plants</h1>
            <p class="page-subtitle mb-0">Manage your nursery plants and monitor available stock.</p>
        </div>
        <a class="btn btn-success" href="{{ route('plants.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Plant</a>
    </div>

    <section class="card">
        <div class="card-body border-bottom">
            <form class="row g-2 align-items-end" action="{{ route('plants.index') }}" method="GET">
                <div class="col-12 col-md-5">
                    <label class="form-label small fw-semibold" for="search">Search by plant name</label>
                    <input class="form-control" id="search" name="search" type="search" value="{{ $filters['search'] ?? '' }}" placeholder="e.g. Peace Lily">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label small fw-semibold" for="category_id">Category</label>
                    <select class="form-select" id="category_id" name="category_id">
                        <option value="">All categories</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @selected((string) ($filters['category_id'] ?? '') === (string) $category->id)>{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-auto d-flex gap-2">
                    <button class="btn btn-success" type="submit"><i class="bi bi-search me-1"></i>Search</button>
                    <a class="btn btn-outline-secondary" href="{{ route('plants.index') }}">Clear</a>
                </div>
            </form>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3 ps-lg-4">ID</th>
                        <th>Plant</th>
                        <th>Category</th>
                        <th>Stock</th>
                        <th>Price</th>
                        <th>Stock Status</th>
                        <th class="text-end pe-3 pe-lg-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($plants as $plant)
                        <tr>
                            <td class="ps-3 ps-lg-4 text-secondary">{{ $plant->id }}</td>
                            <td>
                                <div class="fw-semibold">{{ $plant->name }}</div>
                            </td>
                            <td>{{ $plant->category->name }}</td>
                            <td>{{ number_format($plant->stock) }}</td>
                            <td>₱{{ number_format((float) $plant->price, 2) }}</td>
                            <td>
                                @if ($plant->stock === 0)
                                    <span class="badge text-bg-secondary">Out of Stock</span>
                                @elseif ($plant->stock <= 5)
                                    <span class="badge text-bg-warning">Low Stock</span>
                                @else
                                    <span class="badge text-bg-success">In Stock</span>
                                @endif
                            </td>
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('plants.edit', $plant) }}" aria-label="Edit {{ $plant->name }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('plants.destroy', $plant) }}" method="POST" onsubmit="return confirm('Delete this plant from the inventory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $plant->name }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-5 text-center">
                                <i class="bi bi-search display-6 text-secondary"></i>
                                <p class="fw-semibold mt-2 mb-1">No plants found</p>
                                <p class="text-secondary small mb-0">Try a different search or add a plant to your inventory.</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($plants->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">
                {{ $plants->links() }}
            </div>
        @endif
    </section>
@endsection
