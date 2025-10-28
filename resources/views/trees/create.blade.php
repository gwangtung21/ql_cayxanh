<!-- resources/views/trees/create.blade.php -->
@extends('layouts.app')

@section('title', 'Thêm Cây Mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h4><i class="fas fa-plus-circle text-success"></i> Thêm Cây Xanh Mới</h4>
            </div>
            <div class="card-body">
                <form action="{{ route('trees.store') }}" method="POST">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên cây <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tên khoa học</label>
                            <input type="text" name="scientific_name" class="form-control @error('scientific_name') is-invalid @enderror" 
                                   value="{{ old('scientific_name') }}">
                            @error('scientific_name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">Chọn danh mục</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Vị trí <span class="text-danger">*</span></label>
                            <select name="location_id" class="form-select @error('location_id') is-invalid @enderror" required>
                                <option value="">Chọn vị trí</option>
                                @foreach($locations as $location)
                                    <option value="{{ $location->id }}" {{ old('location_id') == $location->id ? 'selected' : '' }}>
                                        {{ $location->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('location_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Ngày trồng</label>
                            <input type="date" name="planting_date" class="form-control @error('planting_date') is-invalid @enderror" 
                                   value="{{ old('planting_date') }}">
                            @error('planting_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Chiều cao (m)</label>
                            <input type="number" step="0.01" name="height" class="form-control @error('height') is-invalid @enderror" 
                                   value="{{ old('height') }}">
                            @error('height')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Đường kính (cm)</label>
                            <input type="number" step="0.01" name="diameter" class="form-control @error('diameter') is-invalid @enderror" 
                                   value="{{ old('diameter') }}">
                            @error('diameter')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Tình trạng sức khỏe <span class="text-danger">*</span></label>
                        <select name="health_status" class="form-select @error('health_status') is-invalid @enderror" required>
                            <option value="">Chọn tình trạng</option>
                            <option value="excellent" {{ old('health_status') == 'excellent' ? 'selected' : '' }}>Xuất sắc</option>
                            <option value="good" {{ old('health_status') == 'good' ? 'selected' : '' }}>Tốt</option>
                            <option value="fair" {{ old('health_status') == 'fair' ? 'selected' : '' }}>Khá</option>
                            <option value="poor" {{ old('health_status') == 'poor' ? 'selected' : '' }}>Kém</option>
                            <option value="dead" {{ old('health_status') == 'dead' ? 'selected' : '' }}>Chết</option>
                        </select>
                        @error('health_status')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="notes" rows="3" class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <a href="{{ route('trees.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại
                        </a>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu cây
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection