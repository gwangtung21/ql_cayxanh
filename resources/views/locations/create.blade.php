<!-- resources/views/locations/create.blade.php -->
@extends('layouts.app')

@section('title', 'Thêm Vị trí Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus-circle text-info"></i> Thêm Vị trí Mới</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('locations.store') }}" method="POST">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Tên vị trí <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name') }}" required placeholder="VD: Sân trước, Khu A, Vườn hoa...">
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Mô tả</label>
                        <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" 
                                  placeholder="Mô tả về vị trí này...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tọa độ GPS</label>
                        <input type="text" name="coordinates" class="form-control @error('coordinates') is-invalid @enderror" 
                               value="{{ old('coordinates') }}" placeholder="VD: 21.0285, 105.8542">
                        <div class="form-text">Định dạng: latitude, longitude</div>
                        @error('coordinates')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Diện tích (m²)</label>
                        <input type="number" step="0.01" name="area_size" class="form-control @error('area_size') is-invalid @enderror" 
                               value="{{ old('area_size') }}" min="0" placeholder="VD: 100.5">
                        @error('area_size')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('locations.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-info">
                            <i class="fas fa-save"></i> Lưu vị trí
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection