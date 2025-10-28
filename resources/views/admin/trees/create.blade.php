@extends('layouts.admin')
@section('page_title','Thêm Cây Mới')
@section('content')
    <form method="POST" action="{{ route('admin.trees.store') }}" enctype="multipart/form-data">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Tên cây *</label>
                <input name="name" class="form-control" placeholder="VD: Cây Phượng Vỹ 01" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Loại cây *</label>
                <select name="category_id" class="form-select" required>
                    @foreach($categories as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Vị trí *</label>
                <select name="location_id" class="form-select" required>
                    @foreach($locations as $l)
                        <option value="{{ $l->id }}">{{ $l->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Ngày trồng</label>
                <input type="date" name="planting_date" class="form-control">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Chiều cao (m)</label>
                <input name="height" type="number" step="0.01" min="0" class="form-control" value="0">
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">Đường kính (cm)</label>
                <input name="diameter" type="number" step="0.01" min="0" class="form-control" value="0">
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Tình trạng sức khỏe</label>
                <select name="health_status" class="form-select">
                    <option value="good">Khỏe mạnh</option>
                    <option value="fair">Cần chú ý</option>
                    <option value="poor">Cần xử lý gấp</option>
                </select>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea name="notes" class="form-control" placeholder="Thêm ghi chú về tình trạng, chăm sóc cây..."></textarea>
            </div>

            <div class="col-12 mb-3">
                <label class="form-label">Hình ảnh</label>
                <input type="file" name="image" accept="image/*" class="form-control">
            </div>

            <div class="col-12 d-flex justify-content-end">
                <a href="{{ route('admin.trees.index') }}" class="btn btn-secondary me-2">Hủy</a>
                <button class="btn btn-success">Lưu</button>
            </div>
        </div>
    </form>
@endsection