@props(['type' => 'info', 'title' => null])

@php
    $icon = match ($type) {
        'success' => 'check-circle',
        'error' => 'exclamation-octagon',
        default => 'info-circle',
    };
@endphp

<div {{ $attributes->class(['panel-alert', "panel-alert--{$type}"]) }} role="alert">
    <i class="bi bi-{{ $icon }} panel-alert__icon"></i>
    <div>
        @if ($title)
            <strong class="d-block">{{ $title }}</strong>
        @endif
        {{ $slot }}
    </div>
</div>
