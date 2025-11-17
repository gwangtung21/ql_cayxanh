@extends('layouts.admin')

@section('page_title','Cây Xanh')
@section('page_subtitle','Danh sách cây xanh')

@section('content')
<div class="container-fluid">
    <div class="card p-3">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Danh sách cây</h5>
            <form id="tree-search-form" method="GET" class="d-flex" action="{{ route('trees.index') }}">
                <input id="q-input" name="q" value="{{ request('q') }}" class="form-control form-control-sm me-2" placeholder="Tìm kiếm theo tên, loại, địa điểm" />
                {{-- compatibility hidden fields: some controllers may expect other param names --}}
                <input type="hidden" id="search-input" name="search" value="{{ request('q') }}" />
                <input type="hidden" id="keyword-input" name="keyword" value="{{ request('q') }}" />
                <input type="hidden" id="s-input" name="s" value="{{ request('q') }}" />
                <button type="submit" class="btn btn-sm btn-success">Tìm</button>
            </form>
        </div>

        @if(isset($trees) && $trees->count())
            <div id="trees-list" class="row g-3">
                @foreach($trees as $tree)
                    @php
                        // prepare lowercase values for client-side filtering (multibyte-safe)
                        $name_lc = function_exists('mb_strtolower') ? mb_strtolower($tree->name ?? '') : strtolower($tree->name ?? '');
                        $cat_lc = function_exists('mb_strtolower') ? mb_strtolower(optional($tree->category)->name ?? '') : strtolower(optional($tree->category)->name ?? '');
                        $loc_lc = function_exists('mb_strtolower') ? mb_strtolower(optional($tree->location)->name ?? '') : strtolower(optional($tree->location)->name ?? '');
                        $img = $tree->image_url ?? ($tree->images[0] ?? null) ?? null;
                        $img = $img ? asset($img) : 'https://via.placeholder.com/320x200?text=No+Image';
                        $health = $tree->health_status ?? null;
                        $badgeClass = 'bg-secondary';
                        if($health === 'good') $badgeClass = 'bg-success';
                        elseif($health === 'fair') $badgeClass = 'bg-warning text-dark';
                        elseif($health === 'poor') $badgeClass = 'bg-danger';

                        if($health === 'good') $healthLabel = 'Khỏe mạnh';
                        elseif($health === 'fair') $healthLabel = 'Cần chú ý';
                        elseif($health === 'poor') $healthLabel = 'Cần xử lý gấp';
                        else $healthLabel = 'Chưa rõ';
                    @endphp

                    <div class="col-12 col-md-6 col-lg-4 tree-card-item"
                         data-name="{{ $name_lc }}"
                         data-category="{{ $cat_lc }}"
                         data-location="{{ $loc_lc }}">
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

            {{-- client-side no-results message (hidden by default) --}}
            <div id="client-no-results" class="p-4 text-center text-muted d-none">
                Không tìm thấy cây nào khớp.
            </div>

            <div id="trees-pagination" class="mt-3">
                @if(method_exists($trees, 'links'))
                    {{-- preserve query params when paginating --}}
                    {{ $trees->appends(request()->except('page'))->links() }}
                @endif
            </div>
        @else
            <div class="p-4 text-center text-muted">
                Không có cây nào để hiển thị.
            </div>
        @endif
    </div>
</div>

<script>
(function(){
    var qInput = document.getElementById('q-input');
    var searchInput = document.getElementById('search-input');
    var keywordInput = document.getElementById('keyword-input');
    var sInput = document.getElementById('s-input');
    var form = document.getElementById('tree-search-form');
    var cards = Array.prototype.slice.call(document.querySelectorAll('.tree-card-item'));
    var noRes = document.getElementById('client-no-results');
    var pagination = document.getElementById('trees-pagination');

    function syncHidden(v){
        if(searchInput) searchInput.value = v;
        if(keywordInput) keywordInput.value = v;
        if(sInput) sInput.value = v;
    }

    function normalize(str){
        return (str || '').toString().trim().toLowerCase();
    }

    function filterClient(query){
        var q = normalize(query);
        var visibleCount = 0;
        if(cards.length === 0) return visibleCount;
        if(q === ''){
            cards.forEach(function(c){ c.style.display = ''; });
            visibleCount = cards.length;
        } else {
            cards.forEach(function(c){
                var name = c.getAttribute('data-name') || '';
                var cat = c.getAttribute('data-category') || '';
                var loc = c.getAttribute('data-location') || '';
                if(name.indexOf(q) !== -1 || cat.indexOf(q) !== -1 || loc.indexOf(q) !== -1){
                    c.style.display = '';
                    visibleCount++;
                } else {
                    c.style.display = 'none';
                }
            });
        }
        // show/hide no-results and pagination
        if(noRes) noRes.classList.toggle('d-none', visibleCount > 0);
        if(pagination) pagination.style.display = (q === '' || visibleCount > 0) ? '' : 'none';
        return visibleCount;
    }

    // live filter on input
    if(qInput){
        qInput.addEventListener('input', function(){
            var v = this.value || '';
            syncHidden(v);
            filterClient(v);
        });
    }

    // interception on submit: try client filter first, if none matched fallback to server submit
    if(form){
        form.addEventListener('submit', function(e){
            var v = qInput ? qInput.value || '' : '';
            syncHidden(v);
            var visible = filterClient(v);
            if(visible === 0 && v.trim() !== ''){
                // fallback: allow actual submit to server to get comprehensive results
                return true;
            }
            // prevent full page submit when client filtering handled it
            e.preventDefault();
            return false;
        });
    }

    // initial run to respect existing query param on load
    if(qInput){
        filterClient(qInput.value || '');
    }
})();
</script>
{{-- GIT_NOTE: verify .env and sensitive files excluded from git before pushing --}}
@endsection
