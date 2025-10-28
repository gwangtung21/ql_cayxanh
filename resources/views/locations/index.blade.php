<!-- resources/views/locations/index.blade.php -->
@extends('layouts.app')

@section('title', 'Danh sách Vị trí')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-map-marker-alt text-info"></i> Danh sách Vị trí</h2>
    <a href="{{ route('locations.create') }}" class="btn btn-info">
        <i class="fas fa-plus"></i> Thêm Vị trí
    </a>
</div>

<div class="row">
    @forelse($locations as $location)
    <div class="col-md-6 mb-3">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-map-pin text-info"></i> {{ $location->name }}
                </h5>
                <p class="card-text">{{ $location->description }}</p>
                
                <div class="row text-muted small">
                    @if($location->coordinates)
                    <div class="col-md-6">
                        <i class="fas fa-crosshairs"></i> {{ $location->coordinates }}
                    </div>
                    @endif
                    @if($location->area_size)
                    <div class="col-md-6">
                        <i class="fas fa-expand-arrows-alt"></i> {{ $location->area_size }} m²
                    </div>
                    @endif
                </div>
                
                <div class="mt-3">
                    <span class="badge bg-success">
                        <i class="fas fa-seedling"></i> {{ $location->trees_count }} cây
                    </span>
                </div>
                
                <div class="btn-group mt-3">
                    <a href="{{ route('locations.edit', $location) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Sửa
                    </a>
                    <form action="{{ route('locations.destroy', $location) }}" method="POST" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" 
                                onclick="return confirm('Bạn có chắc muốn xóa vị trí này?')">
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
            Chưa có vị trí nào. <a href="{{ route('locations.create') }}">Thêm vị trí đầu tiên</a>
        </div>
    </div>
    @endforelse
</div>
@endsection