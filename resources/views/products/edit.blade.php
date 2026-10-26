@extends('layouts.app')

@section('title', __('products.edit_title'))

@section('content')
    <x-page-header :title="$product['name']" :subtitle="__('products.edit_subtitle')" :eyebrow="__('products.edit_title')" />

    <x-card class="form-card">
        <form method="post" action="{{ route('products.update', $product['id']) }}" novalidate>
            @csrf
            @method('PUT')
            @include('products.partials.form')
        </form>
    </x-card>
@endsection
