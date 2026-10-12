@extends('layouts.app')

@section('title', 'Edit Supplier')

@section('content')
    <div class="mb-4">
        <a class="small text-decoration-none text-success" href="{{ route('suppliers.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to suppliers</a>
        <h1 class="page-title mt-2 mb-1">Edit supplier</h1>
        <p class="page-subtitle mb-0">Update the contact details for {{ $supplier->name }}.</p>
    </div>
    <form action="{{ route('suppliers.update', $supplier) }}" method="POST">
        @csrf
        @method('PUT')
        <section class="card">
            <div class="card-body p-3 p-lg-4">@include('suppliers._form')</div>
            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2 px-3 px-lg-4 pb-4">
                <a class="btn btn-outline-secondary" href="{{ route('suppliers.index') }}">Cancel</a>
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg me-1"></i>Save changes</button>
            </div>
        </section>
    </form>
@endsection
