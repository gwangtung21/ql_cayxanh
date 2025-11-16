@extends('layouts.admin')

@section('page_title','Thêm User Mới')

@section('content')
{{-- wrap form in centered card for balanced layout --}}
<style>
/* center and balance the form */
.user-form-wrap { max-width: 760px; margin: 0 auto; }
.user-card { border-radius:12px; padding:20px; box-shadow:0 12px 30px rgba(2,6,23,0.06); background:#fff; border:1px solid rgba(0,0,0,0.04); }
.form-actions { display:flex; gap:12px; justify-content:flex-end; align-items:center; margin-top:8px; }
@media (max-width: 767px){ .form-actions { flex-direction:column-reverse; align-items:stretch; } }
</style>

<div class="user-form-wrap">
	<div class="user-card">
		<form method="POST" action="{{ route('admin.users.store') }}" id="userCreateForm">
		    @csrf
		    <div class="row g-3">
		        <div class="col-12">
		            <label class="form-label">Họ và tên</label>
		            <input id="u_name" name="name" class="form-control" value="{{ old('name') }}" required>
		        </div>
		        <div class="col-md-6">
		            <label class="form-label">Email</label>
		            <input id="u_email" type="email" name="email" class="form-control" value="{{ old('email') }}" required>
		        </div>
		        <div class="col-md-6">
		            <label class="form-label">Số điện thoại</label>
		            <input id="u_phone" type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="(tuỳ chọn)">
		        </div>
		        <div class="col-md-6">
		            <label class="form-label">Mật khẩu</label>
		            <input id="u_password" type="password" name="password" class="form-control" minlength="6" required>
		        </div>
		        <div class="col-md-6">
		            <label class="form-label">Xác nhận mật khẩu</label>
		            <input id="u_password_confirm" type="password" name="password_confirmation" class="form-control" required>
		        </div>
		        <div class="col-md-6">
		            <label class="form-label">Vai trò</label>
		            <select name="role" class="form-select">
		                <option value="admin">Admin</option>
		                <option value="staff">Staff</option>
		                <option value="user" selected>User</option>
		            </select>
		        </div>
		        <div class="col-12">
		            <label class="form-label">Ghi chú (tuỳ chọn)</label>
		            <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
		        </div>
		    </div>

		    <div class="form-actions mt-4">
		        <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">Hủy</a>
		        <button class="btn btn-primary">Tạo user</button>
		    </div>
		</form>
	</div>
</div>

{{-- keep existing JS validation (unchanged) --}}
<script>
(function(){
    var form = document.getElementById('userCreateForm');
    var email = document.getElementById('u_email');
    var name = document.getElementById('u_name');
    var pass = document.getElementById('u_password');
    var passc = document.getElementById('u_password_confirm');

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
        [name, email, pass, passc].forEach(function(el){ if(el) el.setCustomValidity(''); });
        if(name && name.value.trim() === ''){ name.setCustomValidity('Vui lòng nhập tên'); }
        if(pass && passc && pass.value !== passc.value){ passc.setCustomValidity('Mật khẩu xác nhận không khớp'); }
        if(!form.checkValidity()){ form.reportValidity(); e.preventDefault(); return; }
    });
})();
</script>
@endsection