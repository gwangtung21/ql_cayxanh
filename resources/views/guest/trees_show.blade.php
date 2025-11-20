@extends('layouts.admin')

@section('page_title', isset($tree) ? $tree->name : 'Chi tiết cây')
@section('page_subtitle','Thông tin chi tiết cây xanh')

@section('content')
<div class="container-fluid">
    <div class="card p-3">
        <div class="mb-3 d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1">{{ $tree->name ?? '---' }}</h4>
                <div class="text-muted small">{{ optional($tree->category)->name }} · {{ optional($tree->location)->name }}</div>
            </div>
            <div>
                <a href="{{ route('trees.index') }}" class="btn btn-sm btn-outline-secondary">← Quay lại</a>
            </div>
        </div>

        @php
            $img = $tree->image_url ?? ($tree->images[0] ?? null) ?? null;
            $img = $img ? asset($img) : 'https://via.placeholder.com/800x400?text=No+Image';
            $health = $tree->health_status ?? null;
            $badgeClass = 'bg-secondary';
            if($health === 'good') $badgeClass = 'bg-success';
            elseif($health === 'fair') $badgeClass = 'bg-warning text-dark';
            elseif($health === 'poor') $badgeClass = 'bg-danger';

            // Use same Vietnamese labels as admin/staff views
            if($health === 'good') $healthLabel = 'Khỏe mạnh';
            elseif($health === 'fair') $healthLabel = 'Cần chú ý';
            elseif($health === 'poor') $healthLabel = 'Cần xử lý gấp';
            else $healthLabel = 'Chưa rõ';
        @endphp

        <div class="row g-3">
            <div class="col-12 col-lg-7">
                <div style="height:420px;background-image:url('{{ $img }}');background-size:cover;background-position:center;border-radius:12px"></div>
            </div>
            <div class="col-12 col-lg-5">
                <div class="card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <div class="fw-semibold">Trạng thái sức khỏe</div>
                        <span class="badge {{ $badgeClass }}">{{ $healthLabel }}</span>
                    </div>

                    <dl class="row">
                        <dt class="col-4 text-muted">Loại</dt>
                        <dd class="col-8">{{ optional($tree->category)->name ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Địa điểm</dt>
                        <dd class="col-8">{{ optional($tree->location)->name ?? '-' }}</dd>

                        <dt class="col-4 text-muted">Chiều cao</dt>
                        <dd class="col-8">{{ $tree->height ? $tree->height . ' m' : '-' }}</dd>

                        <dt class="col-4 text-muted">Đường kính</dt>
                        <dd class="col-8">{{ $tree->diameter ? $tree->diameter . ' cm' : '-' }}</dd>

                        <dt class="col-4 text-muted">Ngày ghi nhận</dt>
                        <dd class="col-8">{{ $tree->observed_at ? $tree->observed_at->format('Y-m-d') : ($tree->created_at ? $tree->created_at->format('Y-m-d') : '-') }}</dd>
                    </dl>

                    <!-- <div class="mt-3">
                        <h6 class="mb-1">Mô tả</h6>
                        <p class="small text-muted">{{ $tree->description ?? 'Không có mô tả.' }}</p>
                    </div> -->

                    @if(isset($tree->notes) && $tree->notes)
                        <div class="mt-3">
                            <h6 class="mb-1">Ghi chú</h6>
                            <p class="small text-muted">{{ $tree->notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
