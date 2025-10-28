<!-- resources/views/trees/show.blade.php -->
@extends('layouts.app')

@section('title', 'Chi tiết: ' . $tree->name)

@section('content')
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4><i class="fas fa-tree text-success"></i> {{ $tree->name }}</h4>
                <div class="btn-group">
                    <a href="{{ route('trees.edit', $tree) }}" class="btn btn-warning btn-sm">
                        <i class="fas fa-edit"></i> Chỉnh sửa
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
            
            <div class="card-body">
                @if($tree->image_url)
                    <img src="{{ $tree->image_url }}" class="img-fluid rounded mb-3" style="max-height: 300px;">
                @else
                    <div class="bg-light rounded d-flex align-items-center justify-content-center mb-3" style="height: 300px;">
                        <i class="fas fa-tree fa-5x text-muted"></i>
                    </div>
                @endif
                
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-info-circle"></i> Thông tin cơ bản</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Tên cây:</strong></td>
                                <td>{{ $tree->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tên khoa học:</strong></td>
                                <td>{{ $tree->scientific_name ?: 'Chưa có' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Danh mục:</strong></td>
                                <td>
                                    <span class="badge bg-primary">{{ $tree->category->name }}</span>
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Vị trí:</strong></td>
                                <td>
                                    <span class="badge bg-info">{{ $tree->location->name }}</span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    
                    <div class="col-md-6">
                        <h6><i class="fas fa-ruler"></i> Thông số kỹ thuật</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td><strong>Ngày trồng:</strong></td>
                                <td>{{ $tree->planting_date ? $tree->planting_date->format('d/m/Y') : 'Chưa có' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Chiều cao:</strong></td>
                                <td>{{ $tree->height ? $tree->height . ' m' : 'Chưa đo' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Đường kính:</strong></td>
                                <td>{{ $tree->diameter ? $tree->diameter . ' cm' : 'Chưa đo' }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tình trạng:</strong></td>
                                <td>
                                    <span class="badge bg-{{ $tree->health_status == 'excellent' ? 'success' : ($tree->health_status == 'good' ? 'primary' : ($tree->health_status == 'fair' ? 'warning' : 'danger')) }}">
                                        @switch($tree->health_status)
                                            @case('excellent') Xuất sắc @break
                                            @case('good') Tốt @break
                                            @case('fair') Khá @break
                                            @case('poor') Kém @break
                                            @case('dead') Chết @break
                                        @endswitch
                                    </span>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                @if($tree->notes)
                <div class="mt-3">
                    <h6><i class="fas fa-sticky-note"></i> Ghi chú</h6>
                    <div class="alert alert-light">
                        {{ $tree->notes }}
                    </div>
                </div>
                @endif
                
                <div class="mt-3">
                    <small class="text-muted">
                        <i class="fas fa-calendar-plus"></i> Được thêm: {{ $tree->created_at->format('d/m/Y H:i') }}<br>
                        <i class="fas fa-calendar-edit"></i> Cập nhật lần cuối: {{ $tree->updated_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Lịch chăm sóc -->
        <div class="card mb-3">
            <div class="card-header">
                <h6><i class="fas fa-calendar-check"></i> Lịch chăm sóc</h6>
            </div>
            <div class="card-body">
                @if($tree->careSchedules->count() > 0)
                    @foreach($tree->careSchedules as $schedule)
                    <div class="mb-2 p-2 border rounded">
                        <strong>{{ ucfirst($schedule->care_type) }}</strong><br>
                        <small class="text-muted">
                            Chu kỳ: {{ $schedule->frequency_days }} ngày<br>
                            Tiếp theo: {{ $schedule->next_due_date->format('d/m/Y') }}
                        </small>
                    </div>
                    @endforeach
                @else
                    <p class="text-muted">Chưa có lịch chăm sóc nào.</p>
                @endif
            </div>
        </div>
        
        <!-- Thông tin vị trí -->
        <div class="card">
            <div class="card-header">
                <h6><i class="fas fa-map-marker-alt"></i> Thông tin vị trí</h6>
            </div>
            <div class="card-body">
                <strong>{{ $tree->location->name }}</strong><br>
                @if($tree->location->description)
                    <p class="text-muted mt-2">{{ $tree->location->description }}</p>
                @endif
                @if($tree->location->coordinates)
                    <small class="text-muted">
                        <i class="fas fa-crosshairs"></i> {{ $tree->location->coordinates }}
                    </small>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('trees.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại danh sách
    </a>
</div>
@endsection