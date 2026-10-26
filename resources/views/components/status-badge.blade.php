@props(['status'])

@php
    $tone = match ($status) {
        'paid', 'active' => 'success',
        'confirmed' => 'info',
        'pending' => 'warning',
        default => 'muted',
    };
@endphp

<span {{ $attributes->class(['status-badge', "status-badge--{$tone}"]) }}>
    {{ __('ui.status.'.$status) }}
</span>
