@extends('layouts.admin')

@section('page_title','Sửa User')

@section('content')
    <form method="POST" action="{{ route('admin.users.update', $user) }}">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input id="u_email" type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu (để trống nếu không đổi)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu</label>
            <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role" class="form-select">
                <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
                <option value="staff" {{ $user->role=='staff' ? 'selected' : '' }}>Staff</option>
                <option value="guest" {{ in_array($user->role, ['guest','user']) ? 'selected' : '' }}>Guest</option>
            </select>
        </div>
        <button class="btn btn-primary">Lưu thay đổi</button>
    </form>
    <!-- Client-side email validation messages -->
    <script>
        (function(){
            var form = document.querySelector("form[action='{{ route('admin.users.update', $user) }}']");
            var email = document.getElementById('u_email');
            if (email) {
                email.addEventListener('input', function(){ email.setCustomValidity(''); });
                email.addEventListener('invalid', function(){
                    if (email.validity.valueMissing) email.setCustomValidity('Vui lòng nhập email');
                    else email.setCustomValidity('Email không đúng định dạng');
                });
            }
            if (form) {
                form.addEventListener('submit', function(e){
                    if (email) email.setCustomValidity('');
                    if (!form.checkValidity()) { form.reportValidity(); e.preventDefault(); }
                });
            }
        })();
    </script>
@endsection