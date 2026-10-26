@props(['icon' => 'inbox', 'title', 'text' => null])

<div {{ $attributes->class(['empty-state']) }}>
    <i class="bi bi-{{ $icon }} empty-state__icon" aria-hidden="true"></i>
    <p class="empty-state__title">{{ $title }}</p>
    @if ($text)
        <p class="empty-state__text">{{ $text }}</p>
    @endif
    @if (trim($slot))
        <div class="empty-state__action">{{ $slot }}</div>
    @endif
</div>
