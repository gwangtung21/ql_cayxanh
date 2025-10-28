<!-- resources/views/categories/index.blade.php -->
@extends('layouts.app')

@section('title', 'Danh mục Cây')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tags text-primary"></i> Danh mục Cây</h2>
    <a href="{{ route('categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm Danh mục
    </a>
</div>

<div class="row">
    @forelse($categories as $category)
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">{{ $category->name }}</h5>
                <p class="card-text">{{ $category->description }}</p>
                <p class="text-muted">
                    <i class="fas fa-calendar"></i> Chu kỳ chăm sóc: {{ $category->care_frequency_days }} ngày<br>
                    <i class="fas fa-seedling"></i> Số cây: {{ $category->trees_count }}
                </p>
                <div class="btn-group">
                    <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <form action="{{ route('categories.destroy', $category) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Bạn có chắc muốn xóa danh mục này?')">
                            <i class="fas fa-trash"></i> Xóa
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12">
        <div class="alert alert-info text-center">
            Chưa có danh mục nào. <a href="{{ route('categories.create') }}">Thêm danh mục đầu tiên</a>
        </div>
    </div>
    @endforelse
</div>
@endsection