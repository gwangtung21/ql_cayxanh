<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title','Admin Panel') - QL Cây Xanh</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <style>
        :root{
            --green-1:#e8fbef;
            --green-2:#e0fbec;
            --accent:#16a34a;
            --accent-2:#06b6d4;
            --muted:#6b7280;
            --card-bg:#ffffff;
        }
        html,body{height:100%;}
        body{
            font-family:Inter,ui-sans-serif,system-ui,-apple-system,'Segoe UI',Roboto,Arial;
            background:var(--green-1);
            color:#0f172a;
            -webkit-font-smoothing:antialiased;
            -moz-osx-font-smoothing:grayscale;
        }
        .admin-shell{min-height:100vh;display:flex}
        /* Sidebar */
        .sidebar{
            width:260px;
            background:linear-gradient(180deg, rgba(6,182,212,0.06), rgba(16,163,82,0.03));
            padding:18px 16px;
            border-right:1px solid rgba(0,0,0,0.03);
            display:flex;flex-direction:column;
        }
        .sidebar-brand{display:flex;align-items:center;gap:12px;padding:10px}
        .sidebar-brand .logo{width:48px;height:48px;background:var(--accent);border-radius:12px;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:700}
        .sidebar .fw-bold{font-weight:700}
        .sidebar .text-muted{opacity:0.8}
        .sidebar .p-3{padding:12px}
        .sidebar .rounded{border-radius:10px}
        .sidebar .nav-link{color:var(--muted);padding:10px;border-radius:8px}
        .sidebar .nav-link:hover{background:rgba(0,0,0,0.02);color:#000}
        .sidebar .badge{font-weight:600}
        /* topbar */
        .admin-main{flex:1;padding:24px}
        .topbar{height:64px;display:flex;align-items:center;justify-content:space-between;margin-bottom:20px}
        .topbar h4{margin:0;font-weight:700}
        .topbar .text-muted{color:var(--muted)}
        .btn-sm.btn-danger{background:#ef4444;border-color:#ef4444}
        /* KPI cards */
        .kpi-card{border-radius:12px;background:var(--card-bg);padding:18px;box-shadow:0 8px 24px rgba(11,35,71,0.04);display:flex;flex-direction:column;gap:8px}
        .kpi-card .text-muted{color:var(--muted);font-size:13px}
        .kpi-card .num{font-size:28px;font-weight:700}
        /* content cards */
        .card{border-radius:12px;box-shadow:0 8px 20px rgba(11,35,71,0.03);border:1px solid rgba(0,0,0,0.03)}
        .card p small{color:var(--muted)}
        .content{padding-bottom:40px}
        /* charts */
        canvas{display:block;max-width:100%}
        /* responsive tweaks */
        @media (max-width: 991px){
            .sidebar{width:80px}
            .sidebar .nav-link span{display:none}
            .admin-main{padding:16px}
        }
    </style>
</head>
<body>
<div class="admin-shell">
    <!-- staff sidebar -->
    <aside class="sidebar">
        <div class="sidebar-brand p-3 mb-3">
            <div class="d-flex align-items-center">
                <div class="me-2" style="width:44px;height:44px;border-radius:8px;background:#e6f9f0;display:flex;align-items:center;justify-content:center;"><i class="bi bi-tree-fill text-success"></i></div>
                <div>
                    <div class="fw-bold">Hệ Thống Quản Lý Cây Xanh</div>
                    <small class="text-muted">Quản trị</small>
                </div>
            </div>
        </div>

        @php $user = Auth::user(); @endphp
        <div class="p-3 bg-white border-bottom">
            <div class="d-flex align-items-center">
                <div class="rounded-circle bg-light text-success d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-weight:700">{{ isset($user) && $user->name ? strtoupper(substr(trim($user->name),0,1)) : 'U' }}</div>
                <div class="ms-2">
                    <div class="fw-semibold">{{ $user->name ?? 'Người dùng' }}</div>
                    <div class="text-muted small">{{ $user->role ?? 'Nhân viên chăm sóc' }}</div>
                </div>
            </div>
        </div>

        <nav class="p-3">
            <ul class="nav flex-column">
                <li class="nav-item mb-2"><a class="nav-link text-dark" href="{{ route('admin.dashboard') }}"><i class="bi bi-grid-1x2 me-2"></i> Tổng Quan</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-dark" href="{{ route('admin.trees.index') }}"><i class="bi bi-tree me-2"></i> Quản Lý Cây</a></li>
                <li class="nav-item mb-2"><a class="nav-link text-dark" href="{{ route('admin.users.index') }}"><i class="bi bi-people me-2"></i> Quản Lý User</a></li>
            </ul>
        </nav>
    </aside>
    <!-- end staff sidebar -->

    <main class="admin-main">
        <div class="topbar">
            <div>
                <h4 class="mb-0">@yield('page_title', 'Bảng Điều Khiển')</h4>
                <div class="text-muted small">@yield('page_subtitle','Tổng quan tình trạng cây xanh')</div>
            </div>
            <div>
                <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();" class="btn btn-sm btn-danger">Đăng xuất</a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
            </div>
        </div>

        <div class="content">
            @yield('content')
        </div>
    </main>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
<script>
    // Set avatar background colors from data-bg attributes
    document.addEventListener('DOMContentLoaded', function(){
        document.querySelectorAll('.avatar-circle').forEach(function(el){
            var bg = el.getAttribute('data-bg');
            if (bg) el.style.background = bg;
        });
    });
</script>
</body>
</html>