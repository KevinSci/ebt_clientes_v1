@props([
    'name'  => 'status',
    'value' => 'active',  {{-- Current selected value --}}
    'label' => 'Estado',
])

<div class="mb-3">
    <label for="{{ $name }}" class="form-label fw-medium">
        {{ $label }} <span class="text-danger ms-1" aria-hidden="true">*</span>
    </label>
    <select name="{{ $name }}" id="{{ $name }}" class="form-select @error($name) is-invalid @enderror" required>
        <option value="active" {{ old($name, $value) === 'active' ? 'selected' : '' }}>Activo</option>
        <option value="paused" {{ old($name, $value) === 'paused' ? 'selected' : '' }}>Pausado</option>
        <option value="completed" {{ old($name, $value) === 'completed' ? 'selected' : '' }}>Completado</option>
    </select>
    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
