@props(['active'])

@php
$classes = ($active ?? false)
            ? 'block w-full ps-3 pe-4 py-2.5 rounded-xl text-start text-base font-medium text-white bg-white/10 transition'
            : 'block w-full ps-3 pe-4 py-2.5 rounded-xl text-start text-base font-medium text-slate-400 hover:text-white hover:bg-white/5 transition';
@endphp

<a {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</a>
