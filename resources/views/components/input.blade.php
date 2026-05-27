@props([
    'name'        => '',
    'label'       => '',
    'type'        => 'text',
    'value'       => '',
    'placeholder' => '',
    'required'    => false,
    'helpText'    => null,
])

<div class="mb-3">
    @if ($label)
        <label for="{{ $name }}" class="form-label fw-medium">
            {{ $label }}
            @if ($required)<span class="text-danger ms-1" aria-hidden="true">*</span>@endif
        </label>
    @endif

    <input
        type="{{ $type }}"
        id="{{ $name }}"
        name="{{ $name }}"
        value="{{ old($name, $value) }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    >

    @if ($helpText)
        <div class="form-text">{{ $helpText }}</div>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
