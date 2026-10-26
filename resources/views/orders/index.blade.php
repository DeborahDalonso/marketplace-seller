@extends('layouts.app')

@section('title', __('orders.title'))

@section('content')
    <x-page-header :title="__('orders.title')" :subtitle="__('orders.subtitle')" />

    <x-card flush>
        @if ($orders->isEmpty())
            <x-empty-state icon="receipt" :title="__('orders.empty_title')" :text="__('orders.empty_text')" />
        @else
            <div class="table-responsive">
                <table class="table panel-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('orders.col_order') }}</th>
                            <th>{{ __('orders.col_date') }}</th>
                            <th>{{ __('orders.col_status') }}</th>
                            <th>{{ __('orders.col_payment') }}</th>
                            <th class="text-end">{{ __('orders.col_items') }}</th>
                            <th class="text-end">{{ __('orders.col_total') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($orders as $order)
                            <tr>
                                <td><a href="{{ route('orders.show', $order['id']) }}" class="panel-table__primary mono">{{ $order['id'] }}</a></td>
                                <td class="text-nowrap">@datetime($order['createdAt'])</td>
                                <td><x-status-badge :status="$order['status']" /></td>
                                <td>{{ __('ui.payment.'.$order['paymentMethod']) }}</td>
                                <td class="text-end num">{{ $order['sellerItems'] }}</td>
                                <td class="text-end num">@money($order['sellerCents'])</td>
                                <td class="text-end">
                                    <a href="{{ route('orders.show', $order['id']) }}" class="btn btn-sm btn-outline-secondary">{{ __('ui.action.details') }}</a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($orders->hasPages())
                <div class="panel-table__footer">{{ $orders->links() }}</div>
            @endif
        @endif
    </x-card>
@endsection
