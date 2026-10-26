@extends('layouts.guest')

@section('title', __('login.title'))

@section('content')
    <div class="guest__brand">
        <span class="guest__mark" aria-hidden="true"><i class="bi bi-shop"></i></span>
        <span>{{ config('app.name') }}</span>
    </div>

    <h1 class="guest__title">{{ __('login.title') }}</h1>
    <p class="guest__lead">{{ __('login.lead') }}</p>

    <form method="post" action="{{ route('login.store') }}" novalidate>
        @csrf

        <div class="mb-3">
            <label for="email" class="form-label">{{ __('login.email') }}</label>
            <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus autocomplete="username"
                   class="form-control @error('email') is-invalid @enderror">
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">{{ __('login.password') }}</label>
            <input type="password" name="password" id="password" required autocomplete="current-password"
                   class="form-control @error('password') is-invalid @enderror">
            @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <button type="submit" class="btn btn-primary w-100">{{ __('login.submit') }}</button>
    </form>
@endsection
