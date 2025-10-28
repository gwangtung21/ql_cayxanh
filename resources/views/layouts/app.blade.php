<!-- resources/views/layouts/app.blade.php -->
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý Cây Xanh HPC')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-success">
        <div class="container">
            <a class="navbar-brand" href="{{ route('dashboard') }}">🌳 HPC Tree Management</a>
            <div class="navbar-nav">
                <a class="nav-link" href="{{ route('dashboard') }}">Dashboard</a>
                <a class="nav-link" href="{{ route('trees.index') }}">Cây Xanh</a>
                <a class="nav-link" href="{{ route('categories.index') }}">Danh Mục</a>
                <a class="nav-link" href="{{ route('locations.index') }}">Vị Trí</a>
            </div>
            <div class="d-flex">
                @auth
                    Xin chào, {{ auth()->user()->name }} |
                    <form method="POST" action="{{ route('logout') }}" style="display:inline">@csrf<button class="btn btn-link nav-link">Logout</button></form>
                @else
                    <a class="nav-link" href="{{ route('login') }}">Login</a> | <a class="nav-link" href="{{ route('register') }}">Register</a>
                @endauth
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        
        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>