@php
    $isAuthor = $post->user_id === auth()->id();
@endphp

<x-post-card :post="$post" :canEdit="$isAuthor" />

@if ($isAuthor)
    <x-post-edit-modal 
        :post="$post" 
        :updateUrl="route('client.companies.projects.posts.update', [$company, $project, $post])" 
        :deleteUrl="route('client.companies.projects.posts.destroy', [$company, $project, $post])" 
    />
@endif
