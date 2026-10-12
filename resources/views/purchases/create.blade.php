@extends('layouts.app')

@section('title', 'Record Purchase')

@section('content')
    <div class="mb-4">
        <a class="small text-decoration-none text-success" href="{{ route('purchases.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to purchases</a>
        <h1 class="page-title mt-2 mb-1">Record a purchase</h1>
        <p class="page-subtitle mb-0">Enter the supplier, status, and plants included in this purchase.</p>
    </div>
    <form action="{{ route('purchases.store') }}" method="POST">
        @csrf
        <section class="card">
            <div class="card-body p-3 p-lg-4">
                @include('purchases._form')
            </div>
            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2 px-3 px-lg-4 pb-4">
                <a class="btn btn-outline-secondary" href="{{ route('purchases.index') }}">Cancel</a>
                <button class="btn btn-success" type="submit" @disabled($suppliers->isEmpty() || $plants->isEmpty())><i class="bi bi-check-lg me-1"></i>Save purchase</button>
            </div>
        </section>
    </form>
@endsection
