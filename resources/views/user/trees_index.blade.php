@extends('layouts.admin')

@section('page_title','Cây Xanh')
@section('page_subtitle','Danh sách cây xanh')

@section('content')
<div class="container-fluid">
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh sách cây</h5>
            <form method="GET" class="d-flex" action="{{ route('trees.index') }}">
                <input name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="Tìm kiếm theo tên, loại, địa điểm" />
                <button class="btn btn-sm btn-success">Tìm</button>
            </form>
        </div>

        @if(isset($trees) && $trees->count())
            <div class="row g-3">
                @foreach($trees as $tree)
                        @php
                        $img = $tree->image_url ?? ($tree->images[0] ?? null) ?? null;
                        $img = $img ? asset($img) : 'https://via.placeholder.com/320x200?text=No+Image';
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

                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card h-100">
                            <div style="height:180px;background-image:url('{{ $img }}');background-size:cover;background-position:center;border-top-left-radius:12px;border-top-right-radius:12px;"></div>
                            <div class="p-3">
                                <h6 class="mb-1">{{ $tree->name }}</h6>
                                <div class="text-muted small">{{ optional($tree->category)->name }} · {{ optional($tree->location)->name }}</div>
                                <div class="mt-2 d-flex justify-content-between align-items-center">
                                    <span class="badge {{ $badgeClass }}">{{ $healthLabel }}</span>
                                    <a href="{{ route('trees.show', $tree->id) ?? '#' }}" class="btn btn-sm btn-outline-primary">Xem chi tiết</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="mt-3">
                @if(method_exists($trees, 'links'))
                    {{ $trees->links() }}
                @endif
            </div>
        @else
            <div class="p-4 text-center text-muted">
                Không có cây nào để hiển thị.
            </div>
        @endif
    </div>
</div>
@endsection
