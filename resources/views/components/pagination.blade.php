@props([
    'items' => null,
])

@if ($items && $items->hasPages())
    <div class="mt-4 d-flex justify-content-center" role="navigation" aria-label="Paginación">
        {{ $items->links() }}
    </div>
@endif
