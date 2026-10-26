@extends('layouts.app')

@section('title', __('products.create_title'))

@section('content')
    <x-page-header :title="__('products.create_title')" :subtitle="__('products.create_subtitle')" :eyebrow="__('products.title')" />

    <x-card class="form-card">
        <form method="post" action="{{ route('products.store') }}" novalidate>
            @csrf
            @include('products.partials.form', ['product' => null])
        </form>
    </x-card>
@endsection
