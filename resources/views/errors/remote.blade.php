@extends('layouts.app')

@section('title', __('errors.page.title'))

@section('content')
    <x-page-header :title="__('errors.page.title')" />

    <x-card>
        <x-empty-state icon="plug" :title="$message">
            <a href="{{ url()->current() }}" class="btn btn-primary">{{ __('errors.page.retry') }}</a>
            <a href="{{ route('dashboard') }}" class="btn btn-link">{{ __('errors.page.home') }}</a>
        </x-empty-state>
    </x-card>
@endsection
