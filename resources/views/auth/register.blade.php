@extends('auth.layout')

@section('content')
    <h2 class="h5 mb-1">Đăng ký</h2>
    <p class="text-muted mb-3">Tạo tài khoản mới cho hệ thống</p>

    @if($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('register.submit') }}">
        @csrf

        <div class="mb-3">
            <label for="name" class="form-label">Họ và tên</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required
                   class="form-control @error('name') is-invalid @enderror">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
                   class="form-control @error('email') is-invalid @enderror">
            @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Mật khẩu</label>
            <input id="password" type="password" name="password" required
                   class="form-control @error('password') is-invalid @enderror">
            @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="password_confirmation" class="form-label">Xác nhận mật khẩu</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required
                   class="form-control @error('password_confirmation') is-invalid @enderror">
            @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end">
            <button class="btn btn-primary" type="submit">Đăng ký</button>
        </div>

        <div class="small mt-3">Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập</a></div>
    </form>
@endsection
