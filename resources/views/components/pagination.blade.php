@props([
    'items' => null,
])

@if ($items && $items->hasPages())
    <div class="mt-4 d-flex justify-content-center">
        {{ $items->links('components.pagination-links') }}
    </div>
@endif
