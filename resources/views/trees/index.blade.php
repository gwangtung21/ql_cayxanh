@php
    // ensure $trees exists for this page (fallback to DB when controller didn't provide it)
    if (!isset($trees)) {
        try {
            $trees = \App\Models\Tree::with(['category','location'])->get();
        } catch (\Throwable $e) {
            $trees = collect();
        }
    }
@endphp

{{-- reuse guest view (server-side rendering + search/filter UI) --}}
@include('guest.trees_index')
