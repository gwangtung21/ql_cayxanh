@extends('layouts.admin')

@section('page_title','Thêm User Mới')

@section('content')
    <form method="POST" action="{{ route('admin.users.store') }}" id="userCreateForm">
        @csrf
        <div class="mb-3">
            <label class="form-label">Họ và tên</label>
            <input id="u_name" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input id="u_email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mật khẩu</label>
            <input id="u_password" type="password" name="password" class="form-control" minlength="6" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Xác nhận mật khẩu</label>
            <input id="u_password_confirm" type="password" name="password_confirmation" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Vai trò</label>
            <select name="role" class="form-select">
                <option value="admin">Admin</option>
                <option value="staff">Staff</option>
                <option value="student">Student</option>
            </select>
        </div>
        <button class="btn btn-primary">Tạo user</button>
    </form>

    <script>
        (function(){
            var form = document.getElementById('userCreateForm');
            var email = document.getElementById('u_email');
            var name = document.getElementById('u_name');
            var pass = document.getElementById('u_password');
            var passc = document.getElementById('u_password_confirm');

            // custom messages reset on input
            [email, name, pass, passc].forEach(function(el){ if(el) el.addEventListener('input', function(){ el.setCustomValidity(''); }); });

            if(email){
                email.addEventListener('invalid', function(e){
                    if(email.validity.valueMissing) email.setCustomValidity('Vui lòng nhập email');
                    else email.setCustomValidity('Email không đúng định dạng');
                });
            }

            if(pass){
                pass.addEventListener('invalid', function(){
                    if(pass.validity.valueMissing) pass.setCustomValidity('Vui lòng nhập mật khẩu');
                    else if(pass.validity.tooShort) pass.setCustomValidity('Mật khẩu phải có ít nhất 6 kí tự');
                });
            }

            form.addEventListener('submit', function(e){
                // ensure custom validity messages are reset
                [name, email, pass, passc].forEach(function(el){ if(el) el.setCustomValidity(''); });

                // name required (extra check)
                if(name && name.value.trim() === ''){ name.setCustomValidity('Vui lòng nhập tên'); }

                // password confirmation match
                if(pass && passc && pass.value !== passc.value){
                    passc.setCustomValidity('Mật khẩu xác nhận không khớp');
                }

                // let browser validate and show messages
                if(!form.checkValidity()){
                    form.reportValidity();
                    e.preventDefault();
                    return;
                }
                // otherwise allow submit
            });
        })();
    </script>
@endsection