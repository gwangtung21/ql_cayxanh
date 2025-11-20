@extends('layouts.admin')

@section('page_title','Dashboard Người Dùng')
@section('page_subtitle','Tổng quan — cây và tình trạng')

@section('content')
<?php
	$__collection = $assignedTrees ?? $trees ?? collect();
	try {
		$__trees = $__collection->map(function($t){
			return [
				'id' => $t->id ?? null,
				'name' => $t->name ?? '',
				'category' => $t->category?->name ?? '',
				'location' => $t->location?->name ?? '',
				'planting_date' => isset($t->planting_date) ? (string)$t->planting_date : (isset($t->planted_at) ? (string)$t->planted_at : null),
				'height' => $t->height ?? $t->height_m ?? null,
				'diameter' => $t->diameter ?? $t->diameter_cm ?? null,
				'health_status' => $t->health_status ?? '',
				'notes' => $t->notes ?? '',
				'image_url' => $t->image_url ?? null,
			];
		})->toArray();
	} catch(\Throwable $e) { $__trees = []; }

	// Fallback: if controller didn't pass data, try loading from model directly
	if (empty($__trees)) {
		try {
			$user = \Illuminate\Support\Facades\Auth::user();
			if (isset($user) && isset($user->role) && $user->role === 'staff') {
				// If staff, try to load assigned trees if there is an `assigned_to` column
				$__collection = \App\Models\Tree::where(function($q) use ($user){
					// prefer an `assigned_to` column if exists
					try { $q->where('assigned_to', $user->id); } catch(\Throwable $_) {}
				})->get();
			} else {
				$__collection = \App\Models\Tree::all();
			}
			$__trees = $__collection->map(function($t){
				return [
					'id' => $t->id ?? null,
					'name' => $t->name ?? '',
					'category' => $t->category?->name ?? '',
					'location' => $t->location?->name ?? '',
					'planting_date' => isset($t->planting_date) ? (string)$t->planting_date : (isset($t->planted_at) ? (string)$t->planted_at : null),
					'height' => $t->height ?? $t->height_m ?? null,
					'diameter' => $t->diameter ?? $t->diameter_cm ?? null,
					'health_status' => $t->health_status ?? '',
					'notes' => $t->notes ?? '',
					'image_url' => $t->image_url ?? null,
				];
			})->toArray();
		} catch(\Throwable $_) { $__trees = []; }
	}

	$kpiTotal = count($__trees);
	$kpiGood = collect($__trees)->filter(fn($r)=> preg_match('/(kh|khoe|good|healthy)/i', $r['health_status']))->count();
	$kpiPoor = collect($__trees)->filter(fn($r)=> preg_match('/(xu|poor|nguy|gấp|gap)/i', $r['health_status']))->count();
	$kpiFair = $kpiTotal - $kpiGood - $kpiPoor;

	// categories: count and sample image (first tree image found for the category)
	$byCategory = [];
	foreach ($__trees as $t) {
		$cat = $t['category'] ?: 'Khác';
		if (!isset($byCategory[$cat])) {
			$byCategory[$cat] = ['name' => $cat, 'count' => 0, 'image' => null];
		}
		$byCategory[$cat]['count']++;
		if (!$byCategory[$cat]['image'] && !empty($t['image_url'])) {
			$byCategory[$cat]['image'] = $t['image_url'];
		}
	}
	// convert to indexed array and sort by count desc
	$categories = array_values($byCategory);
	usort($categories, function($a,$b){ return $b['count'] <=> $a['count']; });

	// locations: count by location name (derived from trees payload)
	$byLocation = [];
	foreach ($__trees as $t) {
		$loc = $t['location'] ?: 'Chưa xác định';
		if (!isset($byLocation[$loc])) {
			$byLocation[$loc] = ['name' => $loc, 'count' => 0];
		}
		$byLocation[$loc]['count']++;
	}
	$locations = array_values($byLocation);
	usort($locations, function($a,$b){ return $b['count'] <=> $a['count']; });

	$payload = ['trees' => $__trees, 'kpis' => ['total'=>$kpiTotal,'good'=>$kpiGood,'fair'=>$kpiFair,'poor'=>$kpiPoor], 'categories' => $categories, 'locations' => $locations];
	$payload_json = json_encode($payload, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?>
<script type="application/json" id="user-dashboard-data">{!! $payload_json !!}</script>

<style>
	/* Dashboard specific styles */
	.dashboard-hero .kpi-card { box-shadow: none; }
	.kpi-row .kpi-card{ border-radius:12px; padding:18px; background:#fff; box-shadow:0 8px 20px rgba(11,35,71,0.04); min-height:80px }
	.kpi-row .kpi-card .text-muted{ font-size:13px }
	.kpi-row .kpi-card .num{ font-size:26px; font-weight:700 }
	.category-card{ border-radius:10px; overflow:hidden; box-shadow:0 8px 20px rgba(11,35,71,0.03); }
	.category-card .thumb{ height:88px; background-size:cover; background-position:center }
	.charts-column .card{ height:100%; }
	@media (max-width: 991px){
		.kpi-row .kpi-card{ min-width:140px }
	}
</style>

<div class="container-fluid">
	<div class="row g-3 mb-4">
		<div class="col-12">
			<div class="card p-3 d-flex flex-row align-items-center" style="gap:18px">
				<div style="width:84px;height:84px;border-radius:12px;background:linear-gradient(135deg,#ecfccb,#bbf7d0);display:flex;align-items:center;justify-content:center;flex-shrink:0">
					<i class="bi bi-tree-fill" style="font-size:32px;color:#166534"></i>
				</div>
				<div style="flex:1">
					<h5 class="mb-1">Tổng quan cây xanh</h5>
					<div class="text-muted small">Thống kê nhanh số cây và tình trạng hiện tại</div>
				</div>
				<div class="d-flex" style="gap:12px">
					<div class="kpi-card text-center" style="min-width:120px">
						<div class="text-muted">Tổng số cây</div>
						<div class="num" id="kpi_total">{{ $kpiTotal }}</div>
					</div>
					<div class="kpi-card text-center" style="min-width:120px">
						<div class="text-muted">Khỏe mạnh</div>
						<div class="num text-success" id="kpi_good">{{ $kpiGood }}</div>
					</div>
					<div class="kpi-card text-center" style="min-width:120px">
						<div class="text-muted">Nguy cấp</div>
						<div class="num text-danger" id="kpi_poor">{{ $kpiPoor }}</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="kpi-row row g-3 mb-4">
		<div class="col-12">
			<div class="d-flex flex-wrap align-items-stretch" style="gap:12px">
				<div class="kpi-card text-center" style="flex:1;min-width:160px">
					<div class="text-muted">Tổng số cây</div>
					<div class="num" id="kpi_total">{{ $kpiTotal }}</div>
				</div>
				<div class="kpi-card text-center" style="flex:1;min-width:160px">
					<div class="text-muted">Khỏe mạnh</div>
					<div class="num text-success" id="kpi_good">{{ $kpiGood }}</div>
				</div>
				<div class="kpi-card text-center" style="flex:1;min-width:160px">
					<div class="text-muted">Cần chú ý</div>
					<div class="num text-warning" id="kpi_fair">{{ $kpiFair }}</div>
				</div>
				<div class="kpi-card text-center" style="flex:1;min-width:160px">
					<div class="text-muted">Nguy cấp</div>
					<div class="num text-danger" id="kpi_poor">{{ $kpiPoor }}</div>
				</div>
			</div>
		</div>
	</div>

	<div class="row g-4">
		<div class="col-lg-6">
			<div class="card p-3 mb-3 h-100">
				<h5 class="card-title">Loại cây & Số lượng</h5>
				<div class="row g-3 mt-2" id="categoryCards">
					@forelse($categories as $cat)
						@php
							$img = $cat['image'] ?? null;
							if (!$img) {
								$label = urlencode($cat['name']);
								$img = "https://via.placeholder.com/320x180.png?text={$label}";
							}
						@endphp
						<div class="col-6">
							<div class="card" style="overflow:hidden">
								<div style="height:80px;background-size:cover;background-position:center;background-image:url('{{ $img }}')"></div>
								<div class="p-2">
									<div class="fw-semibold">{{ $cat['name'] }}</div>
									<div class="small text-muted">Số lượng: {{ $cat['count'] }}</div>
								</div>
							</div>
						</div>
					@empty
						<div class="text-muted">Không có dữ liệu phân loại.</div>
					@endforelse
				</div>
			</div>
		</div>

		<div class="col-lg-6">
			<div class="card p-3 mb-3 h-100">
				<h5 class="card-title">Số cây theo khu vực</h5>
				<div class="mt-2">
					@forelse($locations as $loc)
						<div class="d-flex justify-content-between align-items-center py-2" style="border-bottom:1px solid rgba(0,0,0,0.04)">
							<div>
								<div class="fw-semibold">{{ $loc['name'] }}</div>
								<div class="small text-muted">Khu vực</div>
							</div>
							<div>
								<span class="badge bg-success rounded-pill" style="font-size:0.95rem;padding:0.5rem 0.7rem">{{ $loc['count'] }}</span>
							</div>
						</div>
					@empty
						<div class="text-muted">Không có dữ liệu khu vực.</div>
					@endforelse
				</div>
			</div>
		</div>
	</div>
</div>

<!-- detail modal -->
<div class="modal fade" id="userTreeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chi tiết cây</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div id="detailImageWrap" style="height:140px;background:#f3f4f6;border-radius:8px;background-size:cover;background-position:center;"></div>
        <div class="mt-3">
          <h5 id="detailName"></h5>
          <div class="small text-muted" id="detailMeta"></div>
          <div class="mt-2"><strong>Tình trạng:</strong> <span id="detailStatus" class="badge"></span></div>
          <div class="mt-2"><strong>Ghi chú:</strong><div id="detailNotes" class="small text-muted"></div></div>
        </div>
      </div>
      <div class="modal-footer"><button class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button></div>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function(){
	const ds = document.getElementById('user-dashboard-data');
	let payload = {};
	try { payload = ds && ds.textContent ? JSON.parse(ds.textContent) : {}; } catch(e){ payload = {}; }
	const trees = payload.trees || [];
	const kpis = payload.kpis || { total:0, good:0, fair:0, poor:0 };

	// render tree cards (only if the list exists on the page)
	const container = document.getElementById('userTrees');
	function normalize(s){
		s = (s||'').toLowerCase();
		if (s.match(/kh|khoe|good|healthy/)) return 'good';
		if (s.match(/xu|poor|nguy|gap|gấp/)) return 'poor';
		return 'fair';
	}
	// only render list and attach list-related handlers if the list container exists
	if (container) {
		function renderList(list){
			container.innerHTML = '';
			if (!list.length) { container.innerHTML = '<div class="text-muted">Không có cây.</div>'; return; }
			list.forEach(t=>{
				const st = normalize(t.health_status);
				const badge = st==='good' ? 'bg-success text-white' : (st==='poor' ? 'bg-danger text-white' : 'bg-warning text-dark');
				const imgStyle = t.image_url ? 'background-image:url('+t.image_url+');' : '';
				const el = document.createElement('div');
				el.className = 'col-md-6';
				el.innerHTML = `<div class="card p-2 h-100 position-relative">
					<span class="badge ${badge}" style="position:absolute; right:12px; top:12px">${st==='good'?'Khỏe mạnh':(st==='poor'?'Nguy cấp':'Cần chú ý')}</span>
					<div style="height:100px; ${imgStyle} background-size:cover;background-position:center;border-radius:6px"></div>
					<div class="p-2">
						<div class="fw-semibold">${t.name||'-'}</div>
						<div class="small text-muted">${t.category||'-'} • ${t.location||'-'}</div>
						<div class="mt-2 small text-muted">Ngày trồng: ${t.planting_date? t.planting_date.split('T')[0] : '-'}</div>
						<div class="mt-2"><button class="btn btn-sm btn-outline-primary btn-detail" data-id="${t.id}">Xem</button></div>
					</div>
				</div>`;
				container.appendChild(el);
			});
			attachDetails();
		}

		renderList(trees);

		// filters (guard elements in case they are not present)
		const searchEl = document.getElementById('searchTree');
		const filterEl = document.getElementById('filterHealth');
		if (searchEl) searchEl.addEventListener('input', applyFilters);
		if (filterEl) filterEl.addEventListener('change', applyFilters);
		function applyFilters(){
			const q = (searchEl && (searchEl.value||'') ) ? searchEl.value.trim().toLowerCase() : '';
			const st = filterEl ? filterEl.value : '';
			const filtered = trees.filter(t=>{
				const text = ((t.name||'')+' '+(t.category||'')+' '+(t.location||'')).toLowerCase();
				const matchQ = !q || text.includes(q);
				const matchS = !st || normalize(t.health_status) === st;
				return matchQ && matchS;
			});
			renderList(filtered);
		}

		// detail modal
		function attachDetails(){
			document.querySelectorAll('.btn-detail').forEach(b => b.onclick = function(){
				const id = this.getAttribute('data-id');
				const t = trees.find(x=>(''+x.id)===(''+id))||{};
				const nameEl = document.getElementById('detailName');
				const metaEl = document.getElementById('detailMeta');
				const badgeEl = document.getElementById('detailStatus');
				const notesEl = document.getElementById('detailNotes');
				const imgWrap = document.getElementById('detailImageWrap');
				if (nameEl) nameEl.innerText = t.name || '-';
				if (metaEl) metaEl.innerText = (t.category||'-') + ' • ' + (t.location||'-');
				const s = normalize(t.health_status);
				if (badgeEl) {
					badgeEl.className = 'badge ' + (s==='good'?'bg-success text-white':(s==='poor'?'bg-danger text-white':'bg-warning text-dark'));
					badgeEl.innerText = s==='good'?'Khỏe mạnh':(s==='poor'?'Nguy cấp':'Cần chú ý');
				}
				if (notesEl) notesEl.innerText = t.notes || '-';
				if (imgWrap) { if (t.image_url){ imgWrap.style.backgroundImage = 'url('+t.image_url+')'; } else imgWrap.style.backgroundImage = 'none'; }
				new bootstrap.Modal(document.getElementById('userTreeModal')).show();
			});
		}
	}

	// (Removed health & category charts as requested)

});
</script>
@endsection
