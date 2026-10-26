<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="panel">
    <a class="visually-hidden-focusable panel__skip" href="#content">{{ __('ui.skip_to_content') }}</a>

    <aside class="sidebar offcanvas-lg offcanvas-start" tabindex="-1" id="sidebar">
        <div class="sidebar__brand">
            @if ($currentStore?->logoUrl())
                <img class="sidebar__logo" src="{{ $currentStore->logoUrl() }}" alt="">
            @else
                <span class="sidebar__logo sidebar__logo--initial" aria-hidden="true">
                    {{ mb_strtoupper(mb_substr($currentStore?->name ?? ($currentUser['name'] ?? '?'), 0, 1)) }}
                </span>
            @endif
            <div class="sidebar__names">
                <strong>{{ $currentStore?->name ?? __('ui.store_missing') }}</strong>
                <small>{{ $currentUser['name'] ?? '' }}</small>
            </div>
            <button type="button" class="btn-close btn-close-white d-lg-none ms-auto" data-bs-dismiss="offcanvas" data-bs-target="#sidebar" aria-label="{{ __('ui.action.close') }}"></button>
        </div>

        <nav class="sidebar__nav" aria-label="{{ __('ui.nav.section_manage') }}">
            <span class="sidebar__section">{{ __('ui.nav.section_manage') }}</span>
            <a class="sidebar__link @if (request()->routeIs('dashboard')) is-active @endif" href="{{ route('dashboard') }}">
                <i class="bi bi-speedometer2"></i>{{ __('ui.nav.dashboard') }}
            </a>
            <a class="sidebar__link @if (request()->routeIs('products.*')) is-active @endif" href="{{ route('products.index') }}">
                <i class="bi bi-box-seam"></i>{{ __('ui.nav.products') }}
            </a>
            <a class="sidebar__link @if (request()->routeIs('orders.*')) is-active @endif" href="{{ route('orders.index') }}">
                <i class="bi bi-receipt"></i>{{ __('ui.nav.orders') }}
            </a>

            <span class="sidebar__section">{{ __('ui.nav.section_account') }}</span>
            <a class="sidebar__link @if (request()->routeIs('store.*')) is-active @endif" href="{{ route('store.edit') }}">
                <i class="bi bi-shop"></i>{{ __('ui.nav.store') }}
            </a>
            <form method="post" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="sidebar__link">
                    <i class="bi bi-box-arrow-left"></i>{{ __('ui.nav.logout') }}
                </button>
            </form>
        </nav>
    </aside>

    <div class="panel__main">
        <header class="topbar d-lg-none">
            <button class="btn btn-outline-light btn-sm" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebar" aria-controls="sidebar" aria-label="{{ __('ui.open_menu') }}">
                <i class="bi bi-list"></i>
            </button>
            <span class="topbar__title">{{ config('app.name') }}</span>
        </header>

        <main id="content" class="panel__content">
            @yield('content')
        </main>
    </div>

    <div class="toast-container position-fixed bottom-0 end-0 p-3" aria-live="polite">
        @foreach (['success' => 'check-circle', 'error' => 'exclamation-octagon', 'info' => 'info-circle'] as $type => $icon)
            @if (session($type))
                <div class="toast toast--{{ $type }}" role="status" data-autoshow>
                    <div class="toast-header">
                        <i class="bi bi-{{ $icon }} me-2"></i>
                        <strong class="me-auto">{{ __('ui.flash.'.$type) }}</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="{{ __('ui.action.close') }}"></button>
                    </div>
                    <div class="toast-body">{{ session($type) }}</div>
                </div>
            @endif
        @endforeach
    </div>
</body>
</html>
