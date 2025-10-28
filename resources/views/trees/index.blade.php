<!-- resources/views/trees/index.blade.php -->
@extends('layouts.app')

@section('title', 'Danh sách Cây Xanh')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-seedling text-success"></i> Danh sách Cây Xanh</h2>
    <a href="{{ route('trees.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Thêm Cây Mới
    </a>
</div>

<div class="row">
    @forelse($trees as $tree)
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            @if($tree->image_url)
                <img src="{{ $tree->image_url }}" class="card-img-top" style="height: 200px; object-fit: cover;">
            @else
                <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                    <i class="fas fa-tree fa-3x text-muted"></i>
                </div>
            @endif
            
            <div class="card-body">
                <h5 class="card-title">{{ $tree->name }}</h5>
                <p class="card-text">
                    <small class="text-muted">{{ $tree->scientific_name }}</small><br>
                    <strong>Danh mục:</strong> {{ $tree->category->name }}<br>
                    <strong>Vị trí:</strong> {{ $tree->location->name }}<br>
                    <span class="badge bg-{{ $tree->health_status == 'excellent' ? 'success' : ($tree->health_status == 'good' ? 'primary' : 'warning') }}">
                        {{ ucfirst($tree->health_status) }}
                    </span>
                </p>
            </div>
            
            <div class="card-footer">
                <div class="btn-group w-100">
                    <a href="{{ route('trees.show', $tree) }}" class="btn btn-info btn-sm">
                        <i class="fas fa-eye"></i> Xem
                    </a>
                    <a href="{{ route('trees.edit', $tree) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <form action="{{ route('trees.destroy', $tree) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Bạn có chắc muốn xóa cây này?')">
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
            <i class="fas fa-info-circle"></i> Chưa có cây nào được thêm vào hệ thống.
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $trees->links() }}
</div>
@endsection