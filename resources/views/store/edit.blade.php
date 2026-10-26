@extends('layouts.app')

@section('title', __('store.title'))

@section('content')
    <x-page-header :title="__('store.title')" :subtitle="__($store ? 'store.subtitle_edit' : 'store.subtitle_new')" />

    <x-card class="form-card">
        <form method="post" action="{{ route('store.update') }}" enctype="multipart/form-data" novalidate>
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="name" class="form-label">{{ __('store.name') }}</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $store?->name) }}" required maxlength="120"
                           class="form-control @error('name') is-invalid @enderror">
                    @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="col-md-6">
                    <label for="slug" class="form-label">{{ __('store.slug') }}</label>
                    <input type="text" name="slug" id="slug" value="{{ old('slug', $store?->slug) }}" required maxlength="120"
                           class="form-control @error('slug') is-invalid @enderror">
                    @error('slug')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('store.slug_hint') }}</div>
                </div>

                <div class="col-12">
                    <label for="description" class="form-label">{{ __('store.description') }}</label>
                    <textarea name="description" id="description" rows="4" maxlength="2000"
                              class="form-control @error('description') is-invalid @enderror">{{ old('description', $store?->description) }}</textarea>
                    @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('store.description_hint') }}</div>
                </div>

                <div class="col-md-6">
                    <label for="pix_key" class="form-label">{{ __('store.pix_key') }}</label>
                    <input type="text" name="pix_key" id="pix_key" value="{{ old('pix_key', $store?->pix_key) }}" required maxlength="140"
                           class="form-control @error('pix_key') is-invalid @enderror">
                    @error('pix_key')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('store.pix_key_hint') }}</div>
                </div>

                <div class="col-md-6">
                    <label for="logo" class="form-label">{{ __('store.logo') }}</label>
                    <input type="file" name="logo" id="logo" accept="image/*"
                           class="form-control @error('logo') is-invalid @enderror">
                    @error('logo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    <div class="form-text">{{ __('store.logo_hint') }}</div>
                    @if ($store?->logoUrl())
                        <figure class="store-logo mt-2">
                            <img src="{{ $store->logoUrl() }}" alt="">
                            <figcaption>{{ __('store.logo_current') }}</figcaption>
                        </figure>
                    @endif
                </div>
            </div>

            <div class="form-actions">
                <button type="submit" class="btn btn-primary">{{ __('ui.action.save') }}</button>
                @if ($store)
                    <a href="{{ route('dashboard') }}" class="btn btn-link">{{ __('ui.action.cancel') }}</a>
                @endif
            </div>
        </form>
    </x-card>
@endsection
