@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm text-blue-100']) }}>
    {{ $value ?? $slot }}
</label>
