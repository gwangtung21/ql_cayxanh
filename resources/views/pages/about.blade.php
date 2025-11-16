@extends('layouts.admin')

@section('title','Giới Thiệu')
@section('page_title','Giới Thiệu')
@section('page_subtitle','Tổng quan về hệ thống và mục tiêu dự án')

@section('content')
    <div class="card p-4">
        <div class="mb-4">
            <h3 class="fw-bold">Về Hệ Thống Quản Lý Cây Xanh</h3>
            <p class="text-muted">Hệ thống này được xây dựng để giúp quản lý, theo dõi và bảo trì cây xanh trong khu vực một cách hiệu quả. Người quản trị có thể thêm, chỉnh sửa và phân công cây; nhân viên chăm sóc có thể xem các cây được phân công và cập nhật tình trạng; và người dùng/khách có thể xem danh sách cây xanh công khai.</p>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <div class="kpi-card">
                    <div class="text-muted">Tính năng chính</div>
                    <ul class="mb-0">
                        <li>Danh sách & chi tiết cây xanh (hình ảnh, vị trí, tình trạng sức khỏe)</li>
                        <li>Phân loại theo loại cây và vị trí</li>
                        <li>Phân công công việc cho nhân viên chăm sóc</li>
                        <li>Thống kê cơ bản trên trang chủ</li>
                    </ul>
                </div>
            </div>
            <div class="col-md-6">
                <div class="kpi-card">
                    <div class="text-muted">Mục tiêu</div>
                    <p class="mb-0">Giảm thiểu nguy cơ cây bị hư hại thông qua giám sát thường xuyên, cung cấp thông tin dễ tiếp cận cho người quản lý và cộng đồng, và đơn giản hóa quy trình bảo trì.</p>
                </div>
            </div>
        </div>

        <hr class="my-4">

        <div>
            <h5>Liên hệ</h5>
            <p class="text-muted">Nếu bạn có câu hỏi hoặc muốn báo cáo vấn đề với một cây cụ thể, vui lòng liên hệ với quản trị viên hệ thống hoặc sử dụng trang liên hệ (nếu có).</p>
            <a href="{{ route('trees.index') }}" class="btn btn-outline-success">Xem Danh Sách Cây Xanh</a>
        </div>
    </div>
@endsection
