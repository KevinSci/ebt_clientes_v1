@props([
    'name'        => '',
    'label'       => '',
    'rows'        => 5,
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

    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        {{ $required ? 'required' : '' }}
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'form-control' . ($errors->has($name) ? ' is-invalid' : '')]) }}
    >{{ old($name, $value) }}</textarea>

    @if ($helpText)
        <div class="form-text">{{ $helpText }}</div>
    @endif

    @error($name)
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>
