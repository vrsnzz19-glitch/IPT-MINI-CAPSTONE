@extends('layouts.app')

@section('title', 'Edit Plant')

@section('content')
    <div class="mb-4">
        <a class="small text-decoration-none text-success" href="{{ route('plants.index') }}"><i class="bi bi-arrow-left me-1"></i>Back to plants</a>
        <h1 class="page-title mt-2 mb-1">Edit plant</h1>
        <p class="page-subtitle mb-0">Update the details for {{ $plant->plant_name }}.</p>
    </div>

    <form action="{{ route('plants.update', $plant) }}" method="POST">
        @csrf
        @method('PUT')
        <section class="card">
            <div class="card-body p-3 p-lg-4">
                @include('plants._form')
            </div>
            <div class="card-footer bg-white border-0 d-flex justify-content-end gap-2 px-3 px-lg-4 pb-4">
                <a class="btn btn-outline-secondary" href="{{ route('plants.index') }}">Cancel</a>
                <button class="btn btn-success" type="submit"><i class="bi bi-check-lg me-1"></i>Save changes</button>
            </div>
        </section>
    </form>
@endsection
