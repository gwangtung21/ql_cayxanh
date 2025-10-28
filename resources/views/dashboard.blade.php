<!-- resources/views/dashboard.blade.php -->
@extends('layouts.app')

@section('title', 'Dashboard - Quản lý Cây Xanh HPC')

@section('content')
<div class="row">
    <div class="col-md-12">
        <h1>Dashboard Quản lý Cây Xanh</h1>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h5>Tổng số cây</h5>
                <h2>{{ $totalTrees }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h5>Danh mục</h5>
                <h2>{{ $totalCategories }}</h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h5>Vị trí</h5>
                <h2>{{ $totalLocations }}</h2>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Tình trạng sức khỏe cây</h5>
            </div>
            <div class="card-body">
                @foreach($healthStats as $status => $count)
                <div class="d-flex justify-content-between">
                    <span>{{ ucfirst($status) }}:</span>
                    <strong>{{ $count }}</strong>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5>Cây được thêm gần đây</h5>
            </div>
            <div class="card-body">
                @foreach($recentTrees as $tree)
                <div class="mb-2">
                    <strong>{{ $tree->name }}</strong><br>
                    <small class="text-muted">{{ $tree->category->name }} - {{ $tree->location->name }}</small>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection