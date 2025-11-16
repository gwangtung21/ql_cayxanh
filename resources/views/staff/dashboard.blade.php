@extends('layouts.admin')

@section('page_title','Dashboard Nhân Viên Chăm Sóc')

@section('content')
<div class="container-fluid">
    <!-- <div class="row mb-3">
        <div class="col-12">
            <h3 class="mt-2">Dashboard Nhân Viên Chăm Sóc</h3>
            <p class="text-muted">Chào mừng trở lại!</p>
        </div>
    </div> -->

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-success">
                <div class="card-body">
                    <h6 class="card-title text-success">Tổng số cây được phân công</h6>
                    <h2 class="mb-0">{{ $totalAssigned ?? 0 }} <small class="text-muted">cây</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-primary">
                <div class="card-body">
                    <h6 class="card-title text-primary">Cây khỏe mạnh</h6>
                    <h2 class="mb-0">{{ $healthDistribution['good'] ?? 0 }} <small class="text-muted">cây</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-warning">
                <div class="card-body">
                    <h6 class="card-title text-warning">Cây cần chú ý</h6>
                    <h2 class="mb-0">{{ $healthDistribution['fair'] ?? 0 }} <small class="text-muted">cây</small></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger">
                <div class="card-body">
                    <h6 class="card-title text-danger">Cây nguy cấp</h6>
                    <h2 class="mb-0">{{ $healthDistribution['poor'] ?? 0 }} <small class="text-muted">Cần xử lý ngay!</small></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 align-items-stretch">
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-body d-flex flex-column">
                    <h5 class="card-title">Phân Bố Theo Loài</h5>
                    <p class="text-muted">Số lượng cây theo từng loài</p>
                    <div style="min-height:260px;flex:1;">
                        <canvas id="barChart" style="width:100%;height:100%;display:block;" ></canvas>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-body text-center d-flex flex-column">
                    <h5 class="card-title">Tình Trạng Sức Khỏe</h5>
                    <p class="text-muted">Phân bố tình trạng cây</p>
                    <div style="min-height:260px;display:flex;align-items:center;justify-content:center;flex:1;">
                        <canvas id="pieChart" style="width:100%;height:100%;max-width:280px;max-height:280px;"></canvas>
                    </div>
                    <div class="mt-3 text-start">
                        <span class="me-3"><i class="bi bi-square-fill text-warning"></i> Cần chú ý</span>
                        <span class="me-3"><i class="bi bi-square-fill text-success"></i> Khỏe mạnh</span>
                        <span class="me-3"><i class="bi bi-square-fill text-danger"></i> Nguy cấp</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="row g-3 mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <h5 class="card-title">Nhiệm Vụ Hôm Nay</h5>
                    <p class="text-muted">0/4 nhiệm vụ đã hoàn thành</p>
                    <div class="progress" style="height:8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width:0%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div> -->
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<?php
	// build payload in PHP to avoid Blade/@json parsing issues in script
	$__staff_payload = [
		'categoryCounts' => $categoryCounts ?? null,
		'assignedGroup'  => isset($assignedTrees) ? (function($cols){
			try { return $cols->groupBy('category')->map(function($g){ return $g->count(); })->toArray(); } catch(\Throwable $e){ return null; }
		})($assignedTrees) : null,
		'speciesLabels'  => $speciesLabels ?? ['Phượng vỹ','Bàng','Xà cừ','Sao đen','Hoa sữa','Bằng lăng','Dừa xiêm'],
		'speciesCounts'  => $speciesCounts ?? [2,1,1,1,1,1,1],
		'healthDistribution' => $healthDistribution ?? null,
	];
	$__staff_json = json_encode($__staff_payload, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
?>
<script type="application/json" id="staff-dashboard-data">{!! $__staff_json !!}</script>

<script>
	(function(){
		// Read pre-rendered JSON (no Blade directives in runtime script)
		var ds = document.getElementById('staff-dashboard-data');
		var payload = {};
		try {
			payload = ds && ds.textContent ? JSON.parse(ds.textContent) : {};
		} catch(e) {
			console.warn('Invalid staff-dashboard JSON', e);
			payload = {};
		}

		// Prepare category / bar chart data (prefer categoryCounts, then assignedGroup, else fallback)
		var categoryCounts = payload.categoryCounts || null;
		var assignedGroup = payload.assignedGroup || null;
		var labels = [], values = [];
		if (categoryCounts && Object.keys(categoryCounts).length) {
			labels = Object.keys(categoryCounts);
			values = Object.values(categoryCounts);
		} else if (assignedGroup && Object.keys(assignedGroup).length) {
			labels = Object.keys(assignedGroup);
			values = Object.values(assignedGroup);
		} else {
			labels = payload.speciesLabels || [];
			values = payload.speciesCounts || [];
		}

		var displayLabels = labels.map(function(l,i){ return l + ' (' + (values[i]||0) + ')'; });
		var ctxBar = document.getElementById('barChart') && document.getElementById('barChart').getContext ? document.getElementById('barChart').getContext('2d') : null;
		if (ctxBar) {
			var gradient = ctxBar.createLinearGradient(0, 0, 0, 300);
			gradient.addColorStop(0, '#15b7c4');
			gradient.addColorStop(1, '#05a7bd');
			var suggestedMax = Math.max.apply(null, values.concat([1])) + 1;
			new Chart(ctxBar, {
				type: 'bar',
				data: { labels: displayLabels, datasets: [{ label: 'Số lượng', data: values, backgroundColor: gradient, borderRadius: 6, barThickness: 'flex', maxBarThickness: 48 }] },
				options: {
					responsive: true, maintainAspectRatio: false,
					layout: { padding: { top: 8, right: 12, left: 12, bottom: 8 } },
					scales: { x: { grid: { display: false }, ticks: { color: '#6c757d', maxRotation:45, minRotation:45 } }, y: { beginAtZero: true, suggestedMax: suggestedMax, ticks: { stepSize:1, color:'#6c757d' }, grid:{ color:'#e9ecef', borderDash:[4,4] } } },
					plugins: { legend:{ display:true, position:'top', align:'end' }, tooltip:{ mode:'index', intersect:false, callbacks:{ label:function(ctx){ return ' ' + (ctx.parsed.y !== undefined ? ctx.parsed.y : ctx.parsed) + ' cây'; } } } }
				}
			});
		}

		// Pie chart for health distribution
		var health = payload.healthDistribution || null;
		var pieValues = [ (health && (health['fair'] || 0)) || 0, (health && (health['good'] || 0)) || 0, (health && (health['poor'] || 0)) || 0 ];
		var ctxPie = document.getElementById('pieChart') && document.getElementById('pieChart').getContext ? document.getElementById('pieChart').getContext('2d') : null;
		if (ctxPie) {
			new Chart(ctxPie, {
				type: 'pie',
				data: { labels: ['Cần chú ý','Khỏe mạnh','Nguy cấp'], datasets: [{ data: pieValues, backgroundColor: ['#ffb020','#20c997','#ff6b6b'], borderWidth:1, borderColor:'#ffffff' }] },
				options: { responsive:true, maintainAspectRatio:false, plugins:{ legend:{ position:'right', align:'center', labels:{ boxWidth:18 } }, tooltip:{ callbacks:{ label:function(ctx){ return ' ' + (ctx.parsed !== undefined ? ctx.parsed : ctx.raw) + ' cây'; } } } } }
			});
		}
	})();
</script>
@endsection
