@extends('layouts.admin')

@section('page_title','Quản Lý Cây')
@section('page_subtitle', ($trees->count() ?? 0) . ' cây đang được quản lý')

@section('content')
    <div class="mb-3 d-flex justify-content-between align-items-center">
        <div class="search-box w-75 d-flex align-items-center">
            <input id="treeSearch" class="form-control border-0" placeholder="Tìm kiếm theo tên, loài, vị trí..." />
            <select id="treeStatusFilter" class="form-select ms-2" style="width:200px">
                <option value="">Tất cả tình trạng</option>
                <option value="good">Khỏe mạnh</option>
                <option value="fair">Cần chú ý</option>
                <option value="poor">Cần xử lý gấp</option>
            </select>
            <div class="ms-3 small text-muted" id="treeFilterCount">Hiển thị {{ $trees->count() ?? 0 }} / {{ $trees->count() ?? 0 }} cây</div>
        </div>
        <div class="d-flex align-items-center gap-2">
            <div class="btn-group me-2" role="group" aria-label="View switch">
                <button type="button" class="btn btn-outline-secondary" id="btnViewGrid">Lưới</button>
                <button type="button" class="btn btn-outline-secondary" id="btnViewTable">Bảng</button>
            </div>
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#treeModal" id="btnAddTree">Thêm Cây Mới</button>
        </div>
    </div>

    <!-- Grid view -->
    <div class="row g-3" id="treesGrid">
        @foreach($trees as $tree)
            @php
                $hsRaw = strtolower(trim((string)($tree->health_status ?? '')));
                if (strpos($hsRaw,'kh') !== false || strpos($hsRaw,'khoe') !== false || strpos($hsRaw,'good') !== false || strpos($hsRaw,'excellent') !== false) {
                    $healthNorm = 'good';
                } elseif (strpos($hsRaw,'chu') !== false || strpos($hsRaw,'can') !== false || strpos($hsRaw,'fair') !== false || strpos($hsRaw,'cảnh') !== false) {
                    $healthNorm = 'fair';
                } elseif (strpos($hsRaw,'xu') !== false || strpos($hsRaw,'gap') !== false || strpos($hsRaw,'gấp') !== false || strpos($hsRaw,'poor') !== false || strpos($hsRaw,'nghiêm') !== false) {
                    $healthNorm = 'poor';
                } else {
                    $healthNorm = 'fair';
                }
            @endphp
            <div class="col-md-4 tree-col">
                <div class="card shadow-sm position-relative" style="overflow:hidden"
                      data-name="{{ strtolower($tree->name) }}"
                      data-category="{{ strtolower($tree->category?->name ?? '') }}"
                      data-location="{{ strtolower($tree->location?->name ?? '') }}"
                      data-health="{{ $healthNorm }}">

                    {{-- status badge overlay --}}
                    <span class="badge {{ $healthNorm === 'good' ? 'bg-success text-white' : ($healthNorm === 'fair' ? 'bg-warning text-dark' : 'bg-danger text-white') }}" style="position:absolute; top:10px; right:12px; padding:0.32rem 0.6rem; z-index:5; border-radius:8px; font-size:0.85rem; box-shadow:0 2px 8px rgba(0,0,0,0.12);">
                        {{ $healthNorm === 'good' ? 'Khỏe mạnh' : ($healthNorm === 'fair' ? 'Cần chú ý' : 'Cần xử lý gấp') }}
                    </span>

                    {{-- image header --}}
                    <div style="height:160px; background-image:url('{{ $tree->image_url }}'); background-size:cover; background-position:center; border-bottom:1px solid #eef2f5"></div>

                    <div class="card-body">
                        <h5 class="mb-1" style="font-weight:600"><i class="bi bi-tree-fill tree-icon" aria-hidden="true"></i>{{ $tree->name }}</h5>
                        <div class="small text-muted mb-2">{{ $tree->category?->name }} • {{ $tree->location?->name }}</div>

                        <div class="d-flex align-items-center mb-3 text-muted">
                            <div class="me-3" style="display:flex;align-items:center;gap:8px"><span>📅</span><small>{{ optional($tree->planting_date ?? $tree->planted_at)->format('d/m/Y') ?? '-' }}</small></div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded" style="background:#f8fafc;border:1px solid #eef2f5;display:flex;align-items:center;gap:10px">
                                    <div style="width:42px;height:42px;border-radius:8px;background:#eef2ff;display:flex;align-items:center;justify-content:center">📏</div>
                                    <div>
                                        <div class="small text-muted">Chiều cao</div>
                                        <div style="font-weight:600">{{ ($tree->height ?? $tree->height_m) !== null ? number_format(($tree->height ?? $tree->height_m),1) . 'm' : '-' }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded" style="background:#fff7fb;border:1px solid #f3e8ff;display:flex;align-items:center;gap:10px">
                                    <div style="width:42px;height:42px;border-radius:8px;background:#fce7f3;display:flex;align-items:center;justify-content:center">⭕</div>
                                    <div>
                                        <div class="small text-muted">Đường kính</div>
                                        <div style="font-weight:600">{{ ($tree->diameter ?? $tree->diameter_cm) !== null ? number_format(($tree->diameter ?? $tree->diameter_cm),0) . 'cm' : '-' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <div class="small text-muted">Ghi chú</div>
                            <div class="mt-1">{{ $tree->notes ?? '-' }}</div>
                        </div>

                        <div class="d-grid gap-2">
                            <!-- hidden edit trigger for grid card (used by visible Sửa button) -->
                            <button type="button" class="btnEditTree" data-tree='@json($tree->toArray())' style="display:none"></button>
                            <div class="d-flex" style="gap:12px; justify-content:center;">
                                <button class="btn btn-outline-secondary" style="flex:1; display:flex; align-items:center; justify-content:center; gap:8px; padding:10px 12px;" type="button" onclick="(function(el){ var btn = el; btn.closest('.card').querySelector('.btnEditTree').click(); })(this);">✏️ Sửa</button>
                                <form method="POST" action="{{ route('admin.trees.destroy', $tree) }}" onsubmit="return confirm('Xác nhận xóa?')" style="flex:1; margin:0;">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-outline-danger" style="width:100%; display:flex; align-items:center; justify-content:center; gap:8px; padding:10px 12px;" type="submit">🗑️ Xóa</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Table view (hidden by default) -->
    <div class="card mt-3 p-3" id="treesTableWrap" style="display:none">
        <div class="table-responsive">
            <table class="table table-hover" id="treesTable">
                <thead>
                    <tr>
                        <th>Tên cây</th>
                        <th>Loại</th>
                        <th>Vị trí</th>
                        <th>Ngày trồng</th>
                        <th>Chiều cao</th>
                        <th>Đường kính</th>
                        <th>Tình trạng</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trees as $tree)
                        @php
                            $hsRaw = strtolower(trim((string)($tree->health_status ?? '')));
                            if (strpos($hsRaw,'kh') !== false || strpos($hsRaw,'khoe') !== false || strpos($hsRaw,'good') !== false || strpos($hsRaw,'excellent') !== false) {
                                $healthNorm = 'good';
                            } elseif (strpos($hsRaw,'chu') !== false || strpos($hsRaw,'can') !== false || strpos($hsRaw,'fair') !== false || strpos($hsRaw,'cảnh') !== false) {
                                $healthNorm = 'fair';
                            } elseif (strpos($hsRaw,'xu') !== false || strpos($hsRaw,'gap') !== false || strpos($hsRaw,'gấp') !== false || strpos($hsRaw,'poor') !== false || strpos($hsRaw,'nghiêm') !== false) {
                                $healthNorm = 'poor';
                            } else {
                                $healthNorm = 'fair';
                            }
                        @endphp
                        <tr class="tree-row"
                            data-name="{{ strtolower($tree->name) }}"
                            data-category="{{ strtolower($tree->category?->name ?? '') }}"
                            data-location="{{ strtolower($tree->location?->name ?? '') }}"
                            data-health="{{ $healthNorm }}">
                            <td>{{ $tree->name }}</td>
                            <td>{{ $tree->category?->name }}</td>
                            <td>{{ $tree->location?->name }}</td>
                            <td>{{ optional($tree->planting_date)->format('d/m/Y') ?? '-' }}</td>
                            <td>{{ $tree->height !== null ? number_format($tree->height,1) . 'm' : '-' }}</td>
                            <td>{{ $tree->diameter !== null ? number_format($tree->diameter,0) . 'cm' : '-' }}</td>
                            <td>
                                @php
                                    $labelMap = ['good' => 'Khỏe mạnh', 'fair' => 'Cần chú ý', 'poor' => 'Cần xử lý gấp'];
                                    $label = $labelMap[$healthNorm] ?? '-';
                                    $badgeClass = 'bg-light text-dark';
                                    if ($healthNorm === 'good') $badgeClass = 'bg-success text-white';
                                    if ($healthNorm === 'fair') $badgeClass = 'bg-warning text-dark';
                                    if ($healthNorm === 'poor') $badgeClass = 'bg-danger text-white';
                                @endphp
                                <span class="badge {{ $badgeClass }}">{{ $label }}</span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-outline-primary btnEditTree" data-tree='@json($tree->toArray())'>Sửa</button>
                                <form method="POST" action="{{ route('admin.trees.destroy', $tree) }}" style="display:inline" onsubmit="return confirm('Xác nhận xóa?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Xóa</button></form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- chèn dữ liệu JSON để tránh Blade directives trong JS --}}
    <script type="application/json" id="trees-index-data">
    @json([
        'byCategory' => $byCategory ?? [],
        'treesCount' => $trees->count() ?? 0
    ])
    </script>

    <!-- Modal for create/edit tree (unchanged) -->
    <div class="modal fade" id="treeModal" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="treeModalLabel">Thêm / Sửa Cây</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <form id="treeForm" method="POST" action="{{ route('admin.trees.store') }}" enctype="multipart/form-data">
          <div class="modal-body">
                @csrf
                <input type="hidden" name="_method" id="treeFormMethod" value="POST">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="form-label">Tên cây *</label><input id="t_name" name="name" class="form-control" required></div>
                    <div class="col-md-6 mb-3"><label class="form-label">Loại cây *</label><select id="t_category" name="category_id" class="form-select" required>@foreach($categories as $c)<option value="{{ $c->id }}">{{ $c->name }}</option>@endforeach</select></div>
                
                    <div class="col-12 mb-3"><label class="form-label">Vị trí *</label><select id="t_location" name="location_id" class="form-select" required>@foreach($locations as $l)<option value="{{ $l->id }}">{{ $l->name }}</option>@endforeach</select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Ngày trồng</label><input id="t_planted" type="date" name="planting_date" class="form-control"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Chiều cao (m)</label><input id="t_height" name="height" type="number" step="0.01" min="0" class="form-control" value="0"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">Đường kính (cm)</label><input id="t_diameter" name="diameter" type="number" step="0.01" min="0" class="form-control" value="0"></div>
                    <div class="col-12 mb-3"><label class="form-label">Tình trạng sức khỏe</label><select id="t_health" name="health_status" class="form-select"><option value="good">Khỏe mạnh</option><option value="fair">Cần chú ý</option><option value="poor">Cần xử lý gấp</option></select></div>
                    <div class="col-12 mb-3"><label class="form-label">Ghi chú</label><textarea id="t_notes" name="notes" class="form-control" placeholder="Thêm ghi chú về tình trạng, chăm sóc cây..."></textarea></div>
                    <div class="col-12 mb-3"><label class="form-label">URL Hình ảnh</label>
                    <input id="t_image_file" type="file" name="image" accept="image/*" class="form-control mb-2">
                    <input id="t_image" type="hidden" name="image_url">
                    <img id="t_image_preview" src="" alt="" style="max-width:100%;height:120px;object-fit:cover;display:none;border-radius:6px">
                    </div>
                </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Đóng</button>
            <button type="submit" class="btn btn-success">Lưu</button>
          </div>
          </form>
        </div>
      </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function(){
            // đọc dữ liệu từ thẻ JSON (không có Blade bên trong script)
            var pageData = {};
            try {
                var txt = document.getElementById('trees-index-data').textContent || '{}';
                pageData = JSON.parse(txt);
            } catch(e) {
                console.warn('Invalid trees-index JSON', e);
                pageData = {};
            }

            var treeModal = document.getElementById('treeModal');
            var treeForm = document.getElementById('treeForm');
            var treeFormMethod = document.getElementById('treeFormMethod');
            var tImageFile = document.getElementById('t_image_file');
            var tImageHidden = document.getElementById('t_image');
            var tImagePreview = document.getElementById('t_image_preview');

            function filterTrees(){
                var q = document.getElementById('treeSearch').value.trim().toLowerCase();
                var status = document.getElementById('treeStatusFilter').value;
                var cards = document.querySelectorAll('#treesGrid .tree-col');
                var rows = document.querySelectorAll('#treesTable tbody .tree-row');
                var visible = 0;
                cards.forEach(function(col){
                    var card = col.querySelector('.card');
                    var name = (card.getAttribute('data-name') || '').toLowerCase();
                    var category = (card.getAttribute('data-category') || '').toLowerCase();
                    var location = (card.getAttribute('data-location') || '').toLowerCase();
                    var health = card.getAttribute('data-health') || '';
                    var combined = name + ' ' + category + ' ' + location;
                    var matchQuery = q === '' || combined.indexOf(q) !== -1;
                    var matchStatus = status === '' || health === status;
                    if (matchQuery && matchStatus) { col.style.display = ''; visible++; } else { col.style.display = 'none'; }
                });
                rows.forEach(function(tr){
                    var name = tr.getAttribute('data-name') || '';
                    var category = tr.getAttribute('data-category') || '';
                    var location = tr.getAttribute('data-location') || '';
                    var health = tr.getAttribute('data-health') || '';
                    var combined = (name + ' ' + category + ' ' + location).toLowerCase();
                    var matchQuery = q === '' || combined.indexOf(q) !== -1;
                    var matchStatus = status === '' || health === status;
                    if (matchQuery && matchStatus) { tr.style.display = ''; /* count will be handled by cards count */ } else { tr.style.display = 'none'; }
                });
                document.getElementById('treeFilterCount').innerText = 'Hiển thị ' + visible + ' / ' + cards.length + ' cây';
            }

            document.getElementById('treeSearch').addEventListener('input', filterTrees);
            document.getElementById('treeStatusFilter').addEventListener('change', filterTrees);

            document.querySelectorAll('.btnEditTree').forEach(function(btn){
                btn.addEventListener('click', function(){
                    var data = JSON.parse(this.getAttribute('data-tree'));
                    // populate form safely
                    document.getElementById('t_name').value = data.name || '';
                    document.getElementById('t_category').value = data.category_id || (data.category ? data.category.id : '');
                    document.getElementById('t_location').value = data.location_id || (data.location ? data.location.id : '');

                    // robust planted date handling (accept multiple variants)
                    var planted = '';
                    if (data.planting_date) {
                        if (data.planting_date.indexOf('T') !== -1) planted = data.planting_date.split('T')[0];
                        else if (data.planting_date.indexOf(' ') !== -1) planted = data.planting_date.split(' ')[0];
                        else planted = data.planting_date.substring(0,10);
                    } else if (data.planted_at) {
                        if (data.planted_at.indexOf('T') !== -1) planted = data.planted_at.split('T')[0];
                        else if (data.planted_at.indexOf(' ') !== -1) planted = data.planted_at.split(' ')[0];
                        else planted = data.planted_at.substring(0,10);
                    }
                    document.getElementById('t_planted').value = planted;

                    // height and diameter: accept new or old keys
                    var hVal = (data.height !== undefined && data.height !== null) ? data.height : (data.height_m !== undefined && data.height_m !== null ? data.height_m : 0);
                    var dVal = (data.diameter !== undefined && data.diameter !== null) ? data.diameter : (data.diameter_cm !== undefined && data.diameter_cm !== null ? data.diameter_cm : 0);
                    document.getElementById('t_height').value = hVal;
                    document.getElementById('t_diameter').value = dVal;

                    // health status: map common labels to enum values
                    var rawHs = (data.health_status || '').toString();
                    var hsMap = {'khỏe mạnh':'good','khoe manh':'good','khỏe':'good','cần chú ý':'fair','can chu y':'fair','cần xử lý gấp':'poor','can xu ly gap':'poor','excellent':'excellent','good':'good','fair':'fair','poor':'poor'};
                    var hsKey = rawHs.toLowerCase().trim();
                    var hsVal = hsMap[hsKey] || (rawHs ? rawHs : 'good');
                    document.getElementById('t_health').value = hsVal;
                    document.getElementById('t_notes').value = data.notes || '';
                    document.getElementById('t_image').value = data.image_url || '';
                    if (data.image_url) {
                        tImagePreview.src = data.image_url;
                        tImagePreview.style.display = '';
                    } else {
                        tImagePreview.style.display = 'none';
                    }

                    // change form action to update using url helper
                    treeForm.action = '{{ url("admin/trees") }}' + '/' + data.id;
                    treeFormMethod.value = 'PUT';

                    var modal = new bootstrap.Modal(treeModal);
                    modal.show();
                });
            });

            document.getElementById('btnAddTree').addEventListener('click', function(){
                treeForm.action = '{{ route("admin.trees.store") }}';
                treeFormMethod.value = 'POST';
                treeForm.reset();
                // set sensible defaults
                document.getElementById('t_health').value = 'good';
                document.getElementById('t_height').value = 0;
                document.getElementById('t_diameter').value = 0;
                tImagePreview.style.display = 'none';
                tImageHidden.value = '';
            });

            // preview selected file
            if (tImageFile) {
                tImageFile.addEventListener('change', function(e){
                    var f = this.files[0];
                    if (!f) { tImagePreview.style.display = 'none'; return; }
                    var url = URL.createObjectURL(f);
                    tImagePreview.src = url; tImagePreview.style.display = '';
                });
            }

            // view toggle
            document.getElementById('btnViewGrid').addEventListener('click', function(){
                document.getElementById('treesGrid').style.display = '';
                document.getElementById('treesTableWrap').style.display = 'none';
                this.classList.add('active');
                document.getElementById('btnViewTable').classList.remove('active');
            });
            document.getElementById('btnViewTable').addEventListener('click', function(){
                document.getElementById('treesGrid').style.display = 'none';
                document.getElementById('treesTableWrap').style.display = '';
                this.classList.add('active');
                document.getElementById('btnViewGrid').classList.remove('active');
            });

            // initialize default view as grid
            document.getElementById('btnViewGrid').click();

            // Category chart (sử dụng dữ liệu từ pageData)
            var catObj = pageData.byCategory || {};
            var catLabels = Array.isArray(catObj) ? catObj.map(function(_,i){return i;}) : Object.keys(catObj);
            var catData = Array.isArray(catObj) ? catObj : Object.values(catObj || {});

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