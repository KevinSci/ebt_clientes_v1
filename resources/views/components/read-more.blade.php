@props([
    'text'  => '',
    'limit' => 150,
    'class' => '',
])

@php
    $needsTrim = mb_strlen($text) > $limit;
    $preview   = $needsTrim ? mb_substr($text, 0, $limit) : $text;
@endphp

<div class="ebt-read-more {{ $class }}" data-needs-trim="{{ $needsTrim ? 'true' : 'false' }}">
    <p class="ebt-read-more__text mb-0 text-muted {{ $class }}" style="white-space: pre-line"><span class="ebt-read-more__preview">{{ $preview }}</span>@if ($needsTrim)<span class="ebt-read-more__ellipsis">…</span><span class="ebt-read-more__full d-none">{{ mb_substr($text, $limit) }}</span> <button type="button" class="btn btn-link btn-sm p-0 ms-1 ebt-read-more__btn align-baseline" style="font-size: inherit; text-decoration: none; font-weight: 600; line-height: inherit;" aria-expanded="false">Ver más</button>@endif</p>
</div>
