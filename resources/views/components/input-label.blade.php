@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-medium text-slate-600']) }}>
    {{ $value ?? $slot }}
</label>
