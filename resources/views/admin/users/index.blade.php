@extends('layouts.admin')

@section('page_title','Quản Lý User')
@section('page_subtitle', ($users->total() ?? $users->count() ?? 0) . ' người dùng đang được quản lý')

@section('content')
<!-- New styles: improve users management UI -->
<style>
/* Use CSS Grid for consistent layout and centering */
#usersGrid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 18px;
  align-items: start;
  justify-items: stretch;
  padding: 6px 0;
}

/* Each card max width and full-width inside cell */
.user-col {
  width: 100%;
  max-width: 640px;
  justify-self: center; /* center inside grid cell when needed */
}

/* When only one visible item, center it and reduce width */
#usersGrid.single-visible {
  grid-template-columns: 1fr;
  justify-items: center;
}
#usersGrid.single-visible > .user-col {
  max-width: 640px;
}

/* Card */
.user-card {
  background: #fff;
  border-radius: 12px;
  padding: 18px;
  box-shadow: 0 10px 30px rgba(2,6,23,0.06);
  transition: transform .12s ease, box-shadow .12s ease;
  border: 1px solid rgba(15,23,42,0.04);
}
.user-card:hover { transform: translateY(-6px); box-shadow: 0 18px 40px rgba(2,6,23,0.09); }

/* Avatar */
.avatar-circle {
  width:48px; height:48px; border-radius:10px; display:inline-flex;
  align-items:center; justify-content:center; font-weight:700; color:#fff;
  font-size:14px;
  box-shadow:0 6px 18px rgba(2,6,23,0.06);
}

/* Text */
.user-card .fw-semibold { font-weight:700; font-size:1rem; }
.user-card .small.text-muted { color:var(--muted, #6b7280); }

/* Buttons */
.user-card .btn { font-size:0.85rem; padding:.38rem .6rem; }
.user-card .btn-outline-primary { border-radius:8px; }
.user-card .btn-outline-danger { border-radius:8px; }

/* Search box */
.search-box input, .search-box select { background: #fff; border-radius:8px; padding:10px; box-shadow:none; border:1px solid rgba(15,23,42,0.04); }
.search-box .btn-success { border-radius:8px; }

/* Responsive */
@media (max-width: 900px) {
  #usersGrid { grid-template-columns: 1fr; }
  .user-col { max-width: 100%; }
  .search-box { flex-direction: column; gap:8px; }
}

/* Small helpers */
.small-muted { color:var(--muted,#6b7280); font-size:.86rem; }
</style>

    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="search-box w-75 d-flex align-items-center">
            <input id="userSearch" class="form-control border-0" placeholder="Tìm kiếm theo tên, email, số điện thoại..." />
            <select id="userRoleFilter" class="form-select ms-2" style="width:180px">
                <option value="">Tất cả vai trò</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="user">User</option>
            </select>
            <div class="ms-3 small text-muted" id="userFilterCount">Hiển thị {{ $users->total() ?? $users->count() ?? 0 }} / {{ $users->total() ?? $users->count() ?? 0 }} user</div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">Thêm User Mới</a>
    </div>

    <div id="usersGrid" class="g-3">
        @foreach($users as $user)
            @php $bg = '#'.substr(md5($user->id), 0, 6); @endphp
            <div class="user-col">
                <div class="user-card" data-name="{{ strtolower($user->name) }}" data-email="{{ strtolower($user->email) }}" data-role="{{ $user->role }}">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-3">
                            {!! '<div class="avatar-circle" style="background:'.$bg.'">'.strtoupper(substr($user->name,0,2)).'</div>' !!}
                            <div>
                                <div style="font-weight:700">{{ $user->name }}</div>
                                <div class="small text-muted">{{ $user->role }}</div>
                            </div>
                        </div>
                        <div>
                            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-sm btn-outline-primary me-2">Sửa</a>
                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" style="display:inline" onsubmit="return confirm('Xác nhận xóa người dùng này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger">Xóa</button>
                            </form>
                        </div>
                    </div>

                    <hr>
                    <div class="small text-muted">
                        <div><i class="bi bi-envelope"></i> {{ $user->email }}</div>
                        <div><i class="bi bi-telephone"></i> {{ $user->phone ?? '-' }}</div>
                        <div><i class="bi bi-calendar"></i> Tham gia: {{ $user->created_at->format('d/m/Y') }}</div>
                    </div>

                    @if($user->note)
                        <hr>
                        <div class="small">Ghi chú: {{ $user->note }}</div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <div class="mt-4">{{ $users->links() }}</div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            function filterUsers(){
                var q = document.getElementById('userSearch').value.trim().toLowerCase();
                var role = document.getElementById('userRoleFilter').value;
                var cols = document.querySelectorAll('#usersGrid .user-col');
                var visible = 0;
                cols.forEach(function(col){
                    var card = col.querySelector('.user-card');
                    var name = card.getAttribute('data-name') || '';
                    var email = card.getAttribute('data-email') || '';
                    var r = card.getAttribute('data-role') || '';
                    var combined = name + ' ' + email;
                    var matchQuery = q === '' || combined.indexOf(q) !== -1;
                    var matchRole = role === '' || r === role;
                    if (matchQuery && matchRole) { col.style.display = ''; visible++; } else { col.style.display = 'none'; }
                });
                document.getElementById('userFilterCount').innerText = 'Hiển thị ' + visible + ' / ' + cols.length + ' user';
                // add/remove helper class so single card can be centered via CSS
                var grid = document.getElementById('usersGrid');
                if (visible === 1) grid.classList.add('single-visible'); else grid.classList.remove('single-visible');
             }

            document.getElementById('userSearch').addEventListener('input', filterUsers);
            document.getElementById('userRoleFilter').addEventListener('change', filterUsers);
            // run once to apply centering if initial result is single
            filterUsers();
         });
    </script>
@endsection