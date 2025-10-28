<!-- resources/views/categories/create.blade.php -->
@extends('layouts.app')

@section('title', 'Thêm Danh mục Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus-circle text-primary"></i> Thêm Danh mục Cây</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('categories.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Tên danh mục <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required placeholder="VD: Cây ăn quả, Cây cảnh...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Mô tả về loại cây này...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Chu kỳ chăm sóc (ngày) <span class="text-danger">*</span></label>
                        <input type="number" name="care_frequency_days" class="form-control @error('care_frequency_days') is-invalid @enderror" 
                               value="{{ old('care_frequency_days', 30) }}" min="1" required>
                        <div class="form-text">Số ngày giữa các lần chăm sóc (mặc định: 30 ngày)</div>
                        @error('care_frequency_days')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu danh mục
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection