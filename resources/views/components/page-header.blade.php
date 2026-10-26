@props(['title', 'subtitle' => null, 'eyebrow' => null])

<header class="page-header">
    <div>
        @if ($eyebrow)
            <span class="page-header__eyebrow">{{ $eyebrow }}</span>
        @endif
        <h1 class="page-header__title">{{ $title }}</h1>
        @if ($subtitle)
            <p class="page-header__subtitle">{{ $subtitle }}</p>
        @endif
    </div>
    @if (trim($slot))
        <div class="page-header__actions">{{ $slot }}</div>
    @endif
</header>
