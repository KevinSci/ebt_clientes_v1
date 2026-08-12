@props([
    'action',
    'formId' => 'form-new-post',
    'submitLabel' => 'Publicar',
    'project' => null,
])

<div class="card ebt-sticky-xl-top">
    <div class="card-header cursor-pointer d-flex align-items-center justify-content-between" 
         data-bs-toggle="collapse" 
         data-bs-target="#new-pub-collapse" 
         id="new-pub-toggle"
         role="button"
         aria-expanded="false"
         aria-controls="new-pub-collapse">
        <h5 class="card-title mb-0">Nueva Publicación</h5>
        <div class="collapse-icons">
            <i class="bi bi-plus collapse-show-icon fs-5 text-secondary"></i>
            <i class="bi bi-dash collapse-hide-icon fs-5 text-secondary"></i>
        </div>
    </div>
    <div class="card-body collapse" id="new-pub-collapse">

        <form method="POST"
              action="{{ $action }}"
              enctype="multipart/form-data"
              id="{{ $formId }}"
              novalidate>
            @csrf

            <x-input
                name="title"
                label="Título de la publicación"
                :required="true"
                placeholder="Ej. Avance semana 3 — Inspección submarina"
            />

            @if ($project && $project->isPhasesProgress() && $project->phases->isNotEmpty())
                <div class="mb-3">
                    <label for="project_task_id" class="form-label fw-medium">Asociar a Tarea (Opcional)</label>
                    <select name="project_task_id" id="project_task_id" class="form-select @error('project_task_id') is-invalid @enderror">
                        <option value="">-- Proyecto General (Sin tarea) --</option>
                        @foreach($project->phases as $phase)
                            @if($phase->tasks->isNotEmpty())
                                <optgroup label="Fase: {{ $phase->name }}">
                                    @foreach($phase->tasks as $task)
                                        <option value="{{ $task->id }}" {{ old('project_task_id') == $task->id ? 'selected' : '' }}>
                                            {{ $task->name }} {{ $task->is_completed ? '✓' : '' }}
                                        </option>
                                    @endforeach
                                </optgroup>
                            @endif
                        @endforeach
                    </select>
                    @error('project_task_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            @endif

            <x-textarea
                name="description"
                label="Descripción"
                :required="true"
                placeholder="Describe el avance, observaciones o hallazgos del proyecto…"
            />

            <x-input
                name="published_at"
                type="datetime-local"
                label="Fecha de publicación"
                :value="now()->format('Y-m-d\TH:i')"
            />

            {{-- ── File upload with Vanilla JS preview ──────────────── --}}
            <div class="mb-3">
                <label for="attachments" class="form-label fw-medium">
                    Archivos adjuntos
                </label>
                <div class="d-flex gap-2 mb-2">
                    <input
                        type="file"
                        id="attachments"
                        name="attachments[]"
                        class="form-control @error('attachments') is-invalid @enderror @error('attachments.*') is-invalid @enderror"
                        multiple
                        accept="image/*,.pdf,.doc,.docx,.xls,.xlsx,.zip,.rar"
                    >
                    <button type="button" class="btn btn-outline-secondary d-flex align-items-center gap-1 flex-shrink-0" onclick="document.getElementById('folder-picker').click();">
                        <i class="bi bi-folder-plus"></i>
                        <span class="d-none d-sm-inline">Subir Carpeta</span>
                    </button>
                    <input type="file" id="folder-picker" webkitdirectory directory multiple class="d-none">
                </div>
                <div class="form-text text-muted small">
                    Imágenes (JPG, PNG, GIF, WebP), PDFs, Word, Excel y archivos comprimidos (ZIP, RAR). Máx. 20 MB por archivo.
                </div>
                @error('attachments')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
                @error('attachments.*')
                    <div class="invalid-feedback d-block">{{ $message }}</div>
                @enderror
            </div>

            {{-- Preview container (populated by JS) --}}
            <div id="file-preview-container" class="ebt-file-preview mb-3" aria-live="polite"></div>

            <x-button type="submit" variant="primary" class="w-100" icon="bi-send">
                {{ $submitLabel }}
            </x-button>

        </form>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const newPubEl = document.getElementById('new-pub-collapse');
        const toggleEl = document.getElementById('new-pub-toggle');
        if (newPubEl && toggleEl) {
            const hasErrors = {{ $errors->has('title') || $errors->has('description') || $errors->has('attachments') ? 'true' : 'false' }};
            if (window.innerWidth >= 1200) { // Desktop (xl breakpoint)
                // Disable collapse triggers on desktop
                toggleEl.removeAttribute('data-bs-toggle');
                toggleEl.removeAttribute('role');
                toggleEl.classList.remove('cursor-pointer');
                
                // Hide icons on desktop
                const iconsContainer = toggleEl.querySelector('.collapse-icons');
                if (iconsContainer) {
                    iconsContainer.classList.add('d-none');
                }
                
                // Ensure card body is visible and collapse class is removed
                newPubEl.classList.remove('collapse');
                newPubEl.classList.add('show');
            } else { // Mobile/Tablet
                newPubEl.classList.add('collapse');
                if (hasErrors) {
                    newPubEl.classList.add('show');
                    toggleEl.setAttribute('aria-expanded', 'true');
                } else {
                    newPubEl.classList.remove('show');
                    toggleEl.setAttribute('aria-expanded', 'false');
                }
            }
        }
    });
</script>
@endpush
