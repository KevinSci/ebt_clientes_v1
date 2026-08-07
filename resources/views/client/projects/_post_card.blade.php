@php
    $isAuthor = $post->user_id === auth()->id();
@endphp

<x-post-card 
    :post="$post" 
    :canEdit="$isAuthor"
    editModalTarget="#modal-edit-post-global"
    :editUpdateUrl="route('client.companies.projects.posts.update', [$company, $project, $post])"
    :editDeleteUrl="route('client.companies.projects.posts.destroy', [$company, $project, $post])"
/>
