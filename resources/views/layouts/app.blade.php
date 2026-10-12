<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') | {{ config('app.name', 'Plant Nursery Management System') }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --nursery-green: #28734c;
            --nursery-dark: #1d5037;
            --nursery-light: #eaf4ed;
            --page-background: #f5f7f5;
        }

        body {
            background: var(--page-background);
            color: #26352c;
            font-family: Inter, "Segoe UI", sans-serif;
        }

        .main-shell {
            min-width: 0;
        }

        .sidebar {
            --bs-offcanvas-width: 270px;
            background: #fff;
            border-right: 1px solid #e6ebe7;
        }

        .sidebar .offcanvas-header {
            display: flex;
        }

        .sidebar-brand {
            color: var(--nursery-dark);
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.25;
            text-decoration: none;
        }

        .sidebar-brand:hover {
            color: var(--nursery-green);
        }

        .brand-icon {
            align-items: center;
            background: var(--nursery-light);
            border-radius: 12px;
            color: var(--nursery-green);
            display: inline-flex;
            flex: 0 0 42px;
            font-size: 1.35rem;
            height: 42px;
            justify-content: center;
            width: 42px;
        }

        .sidebar-label {
            color: #8a978e;
            font-size: .7rem;
            font-weight: 700;
            letter-spacing: .09em;
            text-transform: uppercase;
        }

        .sidebar .nav-link {
            border-radius: 10px;
            color: #5d6b61;
            font-weight: 500;
            padding: .72rem .85rem;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: var(--nursery-light);
            color: var(--nursery-dark);
        }

        .sidebar .nav-link i {
            font-size: 1.05rem;
            margin-right: .7rem;
            width: 1.15rem;
        }

        .topbar {
            background: #fff;
            border-bottom: 1px solid #e6ebe7;
        }

        .page-title {
            color: #25392c;
            font-size: 1.55rem;
            font-weight: 700;
        }

        .page-subtitle {
            color: #77847b;
        }

        .card {
            border: 1px solid #e8ede9;
            border-radius: 16px;
            box-shadow: 0 5px 20px rgba(32, 62, 43, .045);
        }

        .stat-icon {
            align-items: center;
            border-radius: 13px;
            display: inline-flex;
            font-size: 1.3rem;
            height: 48px;
            justify-content: center;
            width: 48px;
        }

        .stat-value {
            color: #25392c;
            font-size: 1.8rem;
            font-weight: 700;
        }

        .table > :not(caption) > * > * {
            padding: .9rem .8rem;
            vertical-align: middle;
        }

        .table thead th {
            background: #f8faf8;
            color: #748078;
            font-size: .72rem;
            font-weight: 700;
            letter-spacing: .055em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .btn-success {
            --bs-btn-bg: var(--nursery-green);
            --bs-btn-border-color: var(--nursery-green);
            --bs-btn-hover-bg: var(--nursery-dark);
            --bs-btn-hover-border-color: var(--nursery-dark);
            --bs-btn-active-bg: var(--nursery-dark);
            --bs-btn-active-border-color: var(--nursery-dark);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #8dbba0;
            box-shadow: 0 0 0 .2rem rgba(40, 115, 76, .13);
        }

        .pagination {
            margin-bottom: 0;
        }

        @media (min-width: 992px) {
            .sidebar {
                height: 100vh;
                position: sticky;
                top: 0;
                width: 270px !important;
            }
        }
    </style>
</head>
<body>
<div class="d-flex min-vh-100">
    <aside class="offcanvas-lg offcanvas-start sidebar flex-shrink-0" tabindex="-1" id="appSidebar" aria-labelledby="appSidebarLabel">
        <div class="offcanvas-header border-bottom px-4 py-3">
            <a class="sidebar-brand d-flex align-items-center gap-2" href="{{ route('dashboard') }}" id="appSidebarLabel">
                <span class="brand-icon"><i class="bi bi-flower1"></i></span>
                <span>Plant Nursery<br>Management System</span>
            </a>
            <button class="btn-close d-lg-none" type="button" data-bs-dismiss="offcanvas" aria-label="Close navigation"></button>
        </div>
        <div class="offcanvas-body d-flex flex-column p-3">
            <div class="sidebar-label mb-2 mt-2 px-2">Workspace</div>
            <nav class="nav nav-pills flex-column gap-1" aria-label="Main navigation">
                <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2"></i>Dashboard
                </a>
                <a class="nav-link {{ request()->routeIs('plants.*') ? 'active' : '' }}" href="{{ route('plants.index') }}">
                    <i class="bi bi-flower2"></i>Plants
                </a>
                <a class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}" href="{{ route('categories.index') }}">
                    <i class="bi bi-tags"></i>Categories
                </a>
                <a class="nav-link {{ request()->routeIs('suppliers.*') ? 'active' : '' }}" href="{{ route('suppliers.index') }}">
                    <i class="bi bi-truck"></i>Suppliers
                </a>
                <a class="nav-link {{ request()->routeIs('purchases.*') ? 'active' : '' }}" href="{{ route('purchases.index') }}">
                    <i class="bi bi-receipt"></i>Purchases
                </a>
            </nav>
            <div class="mt-auto pt-4">
                <div class="rounded-4 p-3" style="background: var(--nursery-light);">
                    <div class="fw-semibold text-success-emphasis"><i class="bi bi-leaf me-1"></i>Grow with care</div>
                    <div class="small text-secondary mt-1">Keep your plant collection healthy and organized.</div>
                </div>
            </div>
        </div>
    </aside>

    <div class="main-shell d-flex flex-column flex-grow-1 min-vh-100">
        <header class="topbar px-3 px-lg-4 py-3">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3">
                    <button class="btn btn-outline-secondary d-lg-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#appSidebar" aria-controls="appSidebar" aria-label="Open navigation">
                        <i class="bi bi-list"></i>
                    </button>
                    <div>
                        <div class="fw-semibold">{{ config('app.name', 'Plant Nursery Management System') }}</div>
                        <div class="small text-secondary">@yield('title', 'Dashboard')</div>
                    </div>
                </div>
                <a class="btn btn-success d-none d-sm-inline-flex align-items-center gap-2" href="{{ route('plants.create') }}">
                    <i class="bi bi-plus-lg"></i><span>Add plant</span>
                </a>
            </div>
        </header>

        <main class="container-fluid flex-grow-1 px-3 px-lg-4 py-4">
            @if (session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger" role="alert">
                    <div class="fw-semibold mb-1">Please correct the following and try again:</div>
                    <ul class="mb-0 ps-3">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
