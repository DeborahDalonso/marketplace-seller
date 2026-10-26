@props(['title' => null, 'hint' => null, 'flush' => false])

<section {{ $attributes->class(['card panel-card']) }}>
    @if ($title)
        <header class="panel-card__header">
            <div>
                <h2 class="panel-card__title">{{ $title }}</h2>
                @if ($hint)
                    <p class="panel-card__hint">{{ $hint }}</p>
                @endif
            </div>
            @isset($actions)
                <div>{{ $actions }}</div>
            @endisset
        </header>
    @endif
    <div @class(['card-body', 'p-0' => $flush])>
        {{ $slot }}
    </div>
</section>
