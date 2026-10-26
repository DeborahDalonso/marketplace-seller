@extends('layouts.app')

@section('title', __('dashboard.title'))

@section('content')
    <x-page-header :title="__('dashboard.title')" :subtitle="__('dashboard.subtitle')" :eyebrow="$store?->name" />

    @unless ($store)
        <x-alert type="info" :title="__('dashboard.no_store_title')" class="mb-4">
            {{ __('dashboard.no_store_text') }}
            <a href="{{ route('store.edit') }}" class="alert-link ms-1">{{ __('dashboard.no_store_action') }}</a>
        </x-alert>
    @endunless

    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <x-card>
                <span class="kpi__label">{{ __('dashboard.revenue') }}</span>
                <span class="kpi__value">@money($summary['revenueCents'])</span>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card>
                <span class="kpi__label">{{ __('dashboard.paid_orders') }}</span>
                <span class="kpi__value">{{ $summary['paidOrders'] }}</span>
            </x-card>
        </div>
        <div class="col-md-4">
            <x-card>
                <span class="kpi__label">{{ __('dashboard.active_products') }}</span>
                <span class="kpi__value">{{ $summary['activeProducts'] }}</span>
            </x-card>
        </div>
    </div>

    <x-card :title="__('dashboard.chart_title')" :hint="__('dashboard.chart_hint')">
        @if ($summary['revenueCents'] > 0)
            <canvas class="sales-chart" data-series='@json($summary['series'])' role="img" aria-label="{{ __('dashboard.chart_title') }}"></canvas>
        @else
            <x-empty-state icon="bar-chart" :title="__('dashboard.chart_empty_title')" :text="__('dashboard.chart_empty_text')" />
        @endif
    </x-card>
@endsection
