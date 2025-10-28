<!-- resources/views/trees/edit.blade.php -->
@extends('layouts.app')

@section('title', 'Chỉnh sửa Cây')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-edit text-warning"></i> Chỉnh sửa: {{ $tree->name }}</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('trees.update', $tree) }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên cây <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $tree->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khoa học</label>
                            <input type="text" name="scientific_name" class="form-control @error('scientific_name') is-invalid @enderror" 
                                   value="{{ old('scientific_name', $tree->scientific_name) }}">
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" 
                                            {{ old('category_id', $tree->category_id) == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vị trí <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" 
                                            {{ old('location_id', $tree->location_id) == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày trồng</label>
                            <input type="date" name="planting_date" class="form-control" 
                                   value="{{ old('planting_date', $tree->planting_date?->format('Y-m-d')) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Chiều cao (m)</label>
                            <input type="number" step="0.01" name="height" class="form-control" 
                                   value="{{ old('height', $tree->height) }}">
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Đường kính (cm)</label>
                            <input type="number" step="0.01" name="diameter" class="form-control" 
                                   value="{{ old('diameter', $tree->diameter) }}">
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tình trạng sức khỏe <span class="text-danger">*</span></label>
                        <select name="health_status" class="form-select" required>
                            <option value="excellent" {{ old('health_status', $tree->health_status) == 'excellent' ? 'selected' : '' }}>Xuất sắc</option>
                            <option value="good" {{ old('health_status', $tree->health_status) == 'good' ? 'selected' : '' }}>Tốt</option>
                            <option value="fair" {{ old('health_status', $tree->health_status) == 'fair' ? 'selected' : '' }}>Khá</option>
                            <option value="poor" {{ old('health_status', $tree->health_status) == 'poor' ? 'selected' : '' }}>Kém</option>
                            <option value="dead" {{ old('health_status', $tree->health_status) == 'dead' ? 'selected' : '' }}>Chết</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" rows="3" class="form-control">{{ old('notes', $tree->notes) }}</textarea>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('trees.index') }}" class="btn btn-secondary">Quay lại</a>
                        <button type="submit" class="btn btn-warning">Cập nhật</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection