@props([
    'as' => 'button',
    'variant' => 'default',
])

@php
    $baseClasses = 'text-ui-label transition-all ';
    
    $variants = [
        'default' => 'btn rounded-none font-normal border border-slate-200 bg-slate-50 hover:bg-slate-100 shadow-sm px-6 text-base-content',
        'danger'  => 'btn rounded-none font-normal border border-red-200 bg-red-50 hover:bg-red-100 text-red-600 shadow-sm px-6',
        'text'    => 'uppercase hover:text-base-content text-base-content/60',
        'text-danger' => 'uppercase hover:text-error text-base-content/60',
    ];

    $classes = $baseClasses . ($variants[$variant] ?? $variants['default']);
@endphp

@if($as === 'button')
    <button {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </button>
@elseif($as === 'a')
    <a {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@elseif($as === 'label')
    <label {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </label>
@endif