{{--
  Bootstrap pagination for any LengthAwarePaginator / Paginator.
  Usage: @include('dashboard.partials.bootstrap-pagination', ['paginator' => $orders])
--}}
@php
    $paginator = $paginator ?? null;
@endphp
@if($paginator && method_exists($paginator, 'hasPages') && $paginator->hasPages())
    <div class="d-flex flex-column flex-md-row align-items-center justify-content-between mt-3 pt-2 border-top">
        <div class="text-muted small mb-2 mb-md-0">
            Showing
            <strong>{{ $paginator->firstItem() }}</strong>
            –
            <strong>{{ $paginator->lastItem() }}</strong>
            of
            <strong>{{ $paginator->total() }}</strong>
        </div>
        <div>
            {{ $paginator->withQueryString()->links('pagination::bootstrap-4') }}
        </div>
    </div>
@endif
