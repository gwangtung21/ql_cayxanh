<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Auth - QL Cay Xanh</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-" crossorigin="anonymous">
    <style>
        body{background-color:#f8fafc}
        .brand-card{border-radius:12px;background:linear-gradient(135deg,#e6f0fb,#fff);box-shadow:0 6px 18px rgba(11,35,71,0.06)}
        .auth-card{border-radius:12px;box-shadow:0 10px 30px rgba(11,35,71,0.06)}
    </style>
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-lg-10">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="p-4 brand-card h-100">
                        <h1 class="h4 mb-2">Quản lý cây xanh</h1>
                        <p class="text-muted">Hệ thống quản lý cây xanh trường học — quản lý cây, phân loại và vị trí. Đăng nhập để tiếp tục.</p>
                        <hr>
                        <p class="small text-muted">Nếu gặp khó khăn, liên hệ admin.</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="p-4 bg-white auth-card">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap JS (optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-" crossorigin="anonymous"></script>
</body>
</html>
