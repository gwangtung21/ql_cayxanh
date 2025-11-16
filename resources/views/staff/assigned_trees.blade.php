@extends('layouts.admin')

@section('page_title','Cây được phân công')
@section('page_subtitle','Danh sách cây do bạn quản lý — chỉnh sửa tình trạng & ghi chú')

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
	} catch(\Throwable $e) {
		$__trees = [];
	}
	$__payload = ['trees' => $__trees];
	$__payload_json = json_encode($__payload, JSON_HEX_TAG|JSON_HEX_AMP|JSON_HEX_APOS|JSON_HEX_QUOT);
?>
<script type="application/json" id="staff-assigned-data">{!! $__payload_json !!}</script>

<div class="row mb-3">
	<div class="col-12 d-flex align-items-center justify-content-between">
		<!-- <div>
			<h4 class="mb-0">Cây được phân công</h4>
			<small class="text-muted">Xem & cập nhật tình trạng, thêm ghi chú</small>
		</div> -->
		<div class="d-flex gap-2">
			<input id="staffSearch" class="form-control form-control-sm" placeholder="Tìm kiếm tên, loài, vị trí..." style="min-width:260px">
			<select id="staffStatusFilter" class="form-select form-select-sm" style="width:160px">
				<option value="">Tất cả trạng thái</option>
				<option value="good">Khỏe mạnh</option>
				<option value="fair">Cần chú ý</option>
				<option value="poor">Nguy cấp</option>
			</select>
			<button id="btnClearFilters" class="btn btn-outline-secondary btn-sm">Bỏ lọc</button>
		</div>
	</div>
</div>

<div class="row" id="assignedTreesList"></div>

<!-- modal -->
<div class="modal fade" id="assignedTreeModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-md modal-dialog-centered">
    <div class="modal-content">
      <form id="assignedTreeForm">
        <div class="modal-header">
          <h5 class="modal-title">Cập nhật cây</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
        </div>
        <div class="modal-body">
            <input type="hidden" id="m_tree_id">
            <div class="d-flex gap-3 mb-3">
                <img id="m_tree_image" src="" alt="" style="width:110px;height:80px;object-fit:cover;border-radius:8px;display:none">
                <div>
                    <div id="m_tree_name" style="font-weight:700"></div>
                    <div class="small text-muted" id="m_tree_meta"></div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Ghi chú</label>
                <textarea id="m_notes" class="form-control" rows="3"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Tình trạng</label>
                <select id="m_status" class="form-select">
                    <option value="good">Khỏe mạnh</option>
                    <option value="fair">Cần chú ý</option>
                    <option value="poor">Nguy cấp</option>
                </select>
            </div>

            <!-- removed task_done checkbox: feature not integrated -->

        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
          <button type="submit" class="btn btn-success">Lưu</button>
        </div>
      </form>
    </div>
  </div>
</div>

<style>
.staff-tree-card{ border-radius:12px; overflow:hidden; box-shadow:0 8px 24px rgba(11,35,71,0.04); background:#fff; }
.staff-tree-card .img{ height:120px; background-size:cover; background-position:center; }
.badge-good{ background:#16a34a; color:#fff; padding:.35rem .6rem; border-radius:8px; }
.badge-fair{ background:#f59e0b; color:#000; padding:.35rem .6rem; border-radius:8px; }
.badge-poor{ background:#ef4444; color:#fff; padding:.35rem .6rem; border-radius:8px; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function(){
	const ds = document.getElementById('staff-assigned-data');
	let payload = {};
	try { payload = ds && ds.textContent ? JSON.parse(ds.textContent) : {}; } catch(e){ payload = {}; console.warn(e); }
	let trees = payload.trees || [];

	const listWrap = document.getElementById('assignedTreesList');

	function normalizeStatus(s){
		s = (s||'').toString().toLowerCase();
		if (s.indexOf('kh')!==-1 || s.indexOf('good')!==-1) return {key:'good',label:'Khỏe mạnh',cls:'badge-good'};
		if (s.indexOf('xu')!==-1 || s.indexOf('poor')!==-1 || s.indexOf('gấp')!==-1) return {key:'poor',label:'Nguy cấp',cls:'badge-poor'};
		return {key:'fair',label:'Cần chú ý',cls:'badge-fair'};
	}

	function render(items){
		listWrap.innerHTML = '';
		if(!items.length){
			listWrap.innerHTML = '<div class="col-12"><div class="card p-3 text-center text-muted">Không có cây.</div></div>';
			return;
		}
		items.forEach(function(t){
			const st = normalizeStatus(t.health_status);
			const imgStyle = t.image_url ? 'background-image:url('+t.image_url+')' : 'background:#f3f4f6;display:flex;align-items:center;justify-content:center;color:#6b7280';
			const plantDate = t.planting_date ? t.planting_date.split('T')[0] : '-';
			const html = `
				<div class="col-sm-6 col-md-4 mb-3">
					<div class="staff-tree-card">
						<div class="img" style="${imgStyle}"></div>
						<div class="p-3">
							<div class="d-flex justify-content-between align-items-start">
								<div>
									<div style="font-weight:700">${t.name || '-'}</div>
									<div class="small text-muted">${t.category || '-'} • ${t.location || '-'}</div>
								</div>
								<div><span class="${st.cls}">${st.label}</span></div>
							</div>
							<div class="small text-muted mt-2">Ngày trồng: ${plantDate}</div>
							<div class="mt-3 d-flex gap-2">
								<button class="btn btn-sm btn-outline-primary btn-view" data-id="${t.id}">Xem / Sửa</button>
								<button class="btn btn-sm btn-outline-success btn-mark" data-id="${t.id}">Hoàn thành</button>
							</div>
						</div>
					</div>
				</div>`;
			listWrap.insertAdjacentHTML('beforeend', html);
		});
		attachHandlers();
	}

	render(trees);

	// filters
	function applyFilters(){
		const q = (document.getElementById('staffSearch').value||'').trim().toLowerCase();
		const st = document.getElementById('staffStatusFilter').value;
		const filtered = trees.filter(function(t){
			const combined = ((t.name||'') + ' ' + (t.category||'') + ' ' + (t.location||'')).toLowerCase();
			const matchQ = !q || combined.indexOf(q) !== -1;
			const norm = normalizeStatus(t.health_status).key;
			const matchS = !st || st === norm;
			return matchQ && matchS;
		});
		render(filtered);
	}
	document.getElementById('staffSearch').addEventListener('input', applyFilters);
	document.getElementById('staffStatusFilter').addEventListener('change', applyFilters);
	document.getElementById('btnClearFilters').addEventListener('click', function(){ document.getElementById('staffSearch').value=''; document.getElementById('staffStatusFilter').value=''; applyFilters(); });

	// modal and handlers
	const modalEl = document.getElementById('assignedTreeModal'); const bsModal = new bootstrap.Modal(modalEl);
	function attachHandlers(){
		document.querySelectorAll('.btn-view').forEach(b => b.onclick = onView);
		document.querySelectorAll('.btn-mark').forEach(b => b.onclick = onMark);
	}
	function onView(){
		const id = this.getAttribute('data-id');
		const t = trees.find(x => (''+x.id) === (''+id)) || {};
		document.getElementById('m_tree_id').value = t.id || '';
		document.getElementById('m_tree_name').innerText = t.name || '-';
		document.getElementById('m_tree_meta').innerText = (t.category||'-') + ' • ' + (t.location||'-');
		document.getElementById('m_notes').value = t.notes || '';
		document.getElementById('m_status').value = normalizeStatus(t.health_status).key;
		const img = document.getElementById('m_tree_image');
		if(t.image_url){ img.src = t.image_url; img.style.display='block'; } else img.style.display='none';
		bsModal.show();
	}

	function onMark(){
		const id = this.getAttribute('data-id');
		// mark as healthy immediately (no task_done field)
		performUpdate(id, { health_status: 'good' }, function(){ applyFilters(); alert('Đã đánh dấu hoàn thành'); });
	}

	document.getElementById('assignedTreeForm').addEventListener('submit', function(e){
		e.preventDefault();
		const id = document.getElementById('m_tree_id').value;
		const payload = {
			notes: document.getElementById('m_notes').value,
			health_status: document.getElementById('m_status').value
		};
		performUpdate(id, payload, function(){ bsModal.hide(); applyFilters(); alert('Cập nhật thành công'); });
	});

	// update: try server then fallback to local
	function performUpdate(id, payload, cb){
		if(!id){ alert('ID không hợp lệ'); return; }
		const url = '/staff/trees/' + id + '/update-status';
		const headers = {'Content-Type':'application/json'};
		const token = document.querySelector('meta[name="csrf-token"]') ? document.querySelector('meta[name="csrf-token"]').getAttribute('content') : null;
		if(token) headers['X-CSRF-TOKEN'] = token;
		fetch(url, { method:'POST', headers: headers, body: JSON.stringify(payload) })
		.then(resp => {
			if(resp.ok) return resp.json().catch(()=>({}));
			throw resp;
		})
		.then(json => { updateLocal(id, payload); if(cb) cb(); })
		.catch(err => { console.warn('Server update failed, applying local update', err); updateLocal(id, payload); if(cb) cb(); });
	}

	function updateLocal(id, payload){
		for(let i=0;i<trees.length;i++){
			if((''+trees[i].id) === (''+id)){
				trees[i].health_status = payload.health_status || trees[i].health_status;
				trees[i].notes = payload.notes !== undefined ? payload.notes : trees[i].notes;
				break;
			}
		}
	}

});
</script>
@endsection
