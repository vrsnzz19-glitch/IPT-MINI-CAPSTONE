@extends('layouts.app')

@section('title', 'Edit Category')

@section('content')
    <div class="mb-4">
        <a class="small text-decoration-none text-success" href="{{ route('categories.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to categories</a>
        <h1 class="page-title mt-2 mb-1">Edit category</h1>
        <p class="page-subtitle mb-0">Update the details for {{ $category->name }}.</p>
    </div>

    <form action="{{ route('categories.update', $category) }}" method="POST">
        @csrf
        @method('PUT')
        <section class="card">
            <div class="card-body p-3 p-lg-4">
                @include('categories._form')
            </div>
            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2 px-3 px-lg-4 pb-4">
                <a class="btn btn-outline-secondary" href="{{ route('categories.index') }}">Cancel</a>
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg me-1"></i>Save changes</button>
            </div>
        </section>
    </form>
@endsection
