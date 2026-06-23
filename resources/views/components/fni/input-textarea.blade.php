@props(['name' => 'text', 'value' => '', 'min' => 20, 'max' => 10000, 'rows' => 8, 'placeholder' => 'Enter the article headline or full text here...', 'required' => false])

@php $current = old($name, $value); @endphp

<div>
    <textarea
        id="{{ $name }}"
        name="{{ $name }}"
        rows="{{ $rows }}"
        @if($required) required @endif
        minlength="{{ $min }}"
        maxlength="{{ $max }}"
        placeholder="{{ $placeholder }}"
        {{ $attributes->merge(['class' => 'fni-input h-48 resize-none']) }}
    >{{ $current }}</textarea>
    <div class="mt-2 flex justify-between items-center">
        <p class="text-label-caps uppercase tracking-wide text-on-surface-variant">Minimum {{ $min }} characters</p>
        <p class="text-label-caps text-on-surface-variant">{{ strlen($current) }} / {{ $max }}</p>
    </div>
    @error($name)
        <p class="mt-2 text-body-sm text-error">{{ $message }}</p>
    @enderror
</div>
