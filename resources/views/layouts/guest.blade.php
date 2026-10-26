<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') - {{ config('app.name') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="guest">
    <main class="guest__box">
        @yield('content')
    </main>

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
