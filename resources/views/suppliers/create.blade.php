@extends('layouts.app')

@section('title', 'Add Supplier')

@section('content')
    <div class="mb-4">
        <a class="small text-decoration-none text-success" href="{{ route('suppliers.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to suppliers</a>
        <h1 class="page-title mt-2 mb-1">Add a supplier</h1>
        <p class="page-subtitle mb-0">Save contact details for a nursery supplier.</p>
    </div>
    <form action="{{ route('suppliers.store') }}" method="POST">
        @csrf
        <section class="card">
            <div class="card-body p-3 p-lg-4">@include('suppliers._form')</div>
            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2 px-3 px-lg-4 pb-4">
                <a class="btn btn-outline-secondary" href="{{ route('suppliers.index') }}">Cancel</a>
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg me-1"></i>Save supplier</button>
            </div>
        </section>
    </form>
@endsection
