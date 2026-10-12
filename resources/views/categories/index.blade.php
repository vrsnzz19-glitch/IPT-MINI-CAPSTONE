@extends('layouts.app')

@section('title', 'Categories')

@section('content')
    <div class="d-flex flex-wrap align-items-end justify-content-between gap-3 mb-4">
        <div>
            <h1 class="page-title mb-1">Categories</h1>
            <p class="page-subtitle mb-0">Organize plants into clear, easy-to-manage groups.</p>
        </div>
        <a class="btn btn-success" href="{{ route('categories.create') }}"><i class="bi bi-plus-lg me-1"></i>Add Category</a>
    </div>

    <section class="card">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                    <tr>
                        <th class="ps-3 ps-lg-4">Category</th>
                        <th>Description</th>
                        <th>Plants</th>
                        <th class="text-end pe-3 pe-lg-4">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($categories as $category)
                        <tr>
                            <td class="ps-3 ps-lg-4 fw-semibold">{{ $category->name }}</td>
                            <td class="text-secondary">{{ $category->description ?: '—' }}</td>
                            <td><span class="badge rounded-pill text-bg-light">{{ $category->plants_count }}</span></td>
                            <td class="text-end pe-3 pe-lg-4">
                                <div class="d-inline-flex gap-1">
                                    <a class="btn btn-sm btn-outline-success" href="{{ route('categories.edit', $category) }}" aria-label="Edit {{ $category->name }}" title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="{{ route('categories.destroy', $category) }}" method="POST" onsubmit="return confirm('Delete this category?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger" type="submit" aria-label="Delete {{ $category->name }}" title="Delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-5 text-center">
                                <i class="bi bi-tags display-6 text-secondary"></i>
                                <p class="fw-semibold mt-2 mb-1">No categories yet</p>
                                <p class="text-secondary small mb-3">Create a category before adding plants.</p>
                                <a class="btn btn-success btn-sm" href="{{ route('categories.create') }}">Add a category</a>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if ($categories->hasPages())
            <div class="card-footer bg-white border-0 px-3 px-lg-4 py-3">
                {{ $categories->links() }}
            </div>
        @endif
    </section>
@endsection
