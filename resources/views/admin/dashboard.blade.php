@extends('layouts.admin')

@section('page_title','Bảng Điều Khiển')
@section('page_subtitle','Tổng quan tình trạng cây xanh đô thị')

@section('content')
    <div class="row g-3">
        <div class="col-12 col-md-3">
            <div class="kpi-card">
                <div class="text-muted">Tổng Số Cây</div>
                <div style="font-size:28px;font-weight:700">{{ $totalTrees ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="kpi-card">
                <div class="text-muted">Cây Khỏe Mạnh</div>
                <div style="font-size:28px;font-weight:700;color:#10b981">{{ $healthyCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="kpi-card">
                <div class="text-muted">Cần Chú Ý</div>
                <div style="font-size:28px;font-weight:700;color:#f59e0b">{{ $attentionCount ?? 0 }}</div>
            </div>
        </div>
        <div class="col-12 col-md-3">
            <div class="kpi-card">
                <div class="text-muted">Cần Xử Lý Gấp</div>
                <div style="font-size:28px;font-weight:700;color:#ef4444">{{ $urgentCount ?? 0 }}</div>
            </div>
        </div>

        <div class="col-12 mt-3">
            <div class="card p-3">
                <div class="row">
                    <div class="col-md-5 d-flex align-items-center justify-content-center">
                        <div style="width:100%;max-width:360px;">
                            <h6 class="text-center">Tình Trạng Sức Khỏe</h6>
                            <div class="d-flex justify-content-center align-items-center" style="padding:10px 0">
                                <canvas id="healthChart" height="180"></canvas>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-7">
                        <h6>Phân Bố Theo Loại</h6>
                        <div style="padding:10px 0">
                            <canvas id="categoryChart" height="180"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6>Chiều cao trung bình</h6>
                        <div style="font-size:24px">{{ number_format((float)($avgHeight ?? 0),1) }}m</div>
                    </div>
                    <div class="col-md-6">
                        <h6>Đường kính trung bình</h6>
                        <div style="font-size:24px">{{ number_format((float)($avgDiameter ?? 0),1) }}cm</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // Health chart data
            var rawDist = @json($healthDistribution ?? (object)[]);

            // Normalize and aggregate into 3 categories: Khỏe mạnh, Cần chú ý, Cần xử lý gấp
            function normalizeHealthKey(k) {
                var s = (k || '').toString().toLowerCase();
                if (s.indexOf('kh') !== -1 || s.indexOf('khoe') !== -1 || s.indexOf('good') !== -1 || s.indexOf('excellent') !== -1) return 'Khỏe mạnh';
                if (s.indexOf('chú') !== -1 || s.indexOf('chu') !== -1 || s.indexOf('fair') !== -1 || s.indexOf('cảnh') !== -1) return 'Cần chú ý';
                if (s.indexOf('xử') !== -1 || s.indexOf('xu') !== -1 || s.indexOf('gấp') !== -1 || s.indexOf('gap') !== -1 || s.indexOf('poor') !== -1) return 'Cần xử lý gấp';
                return 'Cần chú ý';
            }

            var aggregated = { 'Khỏe mạnh': 0, 'Cần chú ý': 0, 'Cần xử lý gấp': 0 };
            for (var k in rawDist) {
                if (!rawDist.hasOwnProperty(k)) continue;
                var nk = normalizeHealthKey(k);
                var v = Number(rawDist[k]) || 0;
                aggregated[nk] = (aggregated[nk] || 0) + v;
            }

            var healthLabels = Object.keys(aggregated);
            var healthData = healthLabels.map(function(l){ return aggregated[l]; });

            // If totally empty, show placeholder
            var totalHealth = healthData.reduce(function(a,b){return a+b;}, 0);
            if (totalHealth === 0) {
                healthLabels = ['Không có dữ liệu'];
                healthData = [0];
            }

            var ctxH = document.getElementById('healthChart').getContext('2d');

            var colorMap = { 'Khỏe mạnh':'#16a34a', 'Cần chú ý':'#f59e0b', 'Cần xử lý gấp':'#ef4444', 'Không có dữ liệu':'#9ca3af' };
            var bgColors = healthLabels.map(function(l){ return colorMap[l] || '#60a5fa'; });

            new Chart(ctxH, {
                type: 'pie',
                data: {
                    labels: healthLabels,
                    datasets: [{ data: healthData, backgroundColor: bgColors }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    aspectRatio: 1,
                    plugins: { legend: { position: 'right' } }
                }
            });

            // Category chart
            var catLabels = @json(array_keys($byCategory ?? []));
            var catData = @json(array_values($byCategory ?? []));
            if (catLabels.length === 0) {
                catLabels = ['Không có dữ liệu'];
                catData = [0];
            }
            var ctxC = document.getElementById('categoryChart').getContext('2d');
            new Chart(ctxC, {
                type: 'bar',
                data: { labels: catLabels, datasets: [{ label: 'Số lượng', data: catData, backgroundColor: '#06b6d4' }] },
                options: { responsive:true, maintainAspectRatio:false, aspectRatio: 2, scales: { y: { beginAtZero: true, precision:0 } } }
            });
        });
    </script>
@endsection
