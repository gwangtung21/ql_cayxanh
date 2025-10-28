@extends('auth.layout')

@section('content')
    <h2 class="h5 mb-1">Đăng nhập</h2>
    <p class="text-muted mb-3">Đăng nhập bằng email và mật khẩu của bạn</p>

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
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

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div class="form-check">
                <input class="form-check-input" type="checkbox" name="remember" id="remember">
                <label class="form-check-label small" for="remember">Ghi nhớ</label>
            </div>
            <button class="btn btn-primary" type="submit">Đăng nhập</button>
        </div>

        @if($errors->any() && !($errors->has('email') || $errors->has('password')))
            <div class="alert alert-danger">{{ $errors->first() }}</div>
        @endif

        <div class="small">Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký tại đây</a></div>
    </form>
@endsection
