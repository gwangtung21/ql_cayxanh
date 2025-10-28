@extends('layouts.admin')

@section('page_title','Quản Lý User')
@section('page_subtitle', ($users->total() ?? $users->count() ?? 0) . ' người dùng đang được quản lý')

@section('content')
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="search-box w-75 d-flex align-items-center">
            <input id="userSearch" class="form-control border-0" placeholder="Tìm kiếm theo tên, email, số điện thoại..." />
            <select id="userRoleFilter" class="form-select ms-2" style="width:180px">
                <option value="">Tất cả vai trò</option>
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="student">Student</option>
            </select>
            <div class="ms-3 small text-muted" id="userFilterCount">Hiển thị {{ $users->total() ?? $users->count() ?? 0 }} / {{ $users->total() ?? $users->count() ?? 0 }} user</div>
        </div>
        <a href="{{ route('admin.users.create') }}" class="btn btn-success">Thêm User Mới</a>
    </div>

    <div class="row g-3" id="usersGrid">
        @foreach($users as $user)
            @php $bg = '#'.substr(md5($user->id), 0, 6); @endphp
            <div class="col-md-6 user-col">
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
                            <form method="POST" action="#" style="display:inline">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button></form>
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
            }

            document.getElementById('userSearch').addEventListener('input', filterUsers);
            document.getElementById('userRoleFilter').addEventListener('change', filterUsers);
        });
    </script>
@endsection