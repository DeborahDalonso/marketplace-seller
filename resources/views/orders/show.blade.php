@extends('layouts.app')

@section('title', __('orders.detail_title', ['id' => $order['id']]))

@section('content')
    <x-page-header :title="$order['id']" :eyebrow="__('orders.title')">
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i>{{ __('ui.action.back') }}
        </a>
    </x-page-header>

    <div class="row g-3">
        <div class="col-lg-4">
            <x-card :title="__('orders.summary')">
                <dl class="summary-list">
                    <dt>{{ __('orders.col_status') }}</dt>
                    <dd><x-status-badge :status="$order['status']" /></dd>
                    <dt>{{ __('orders.payment') }}</dt>
                    <dd>{{ __('ui.payment.'.$order['paymentMethod']) }}</dd>
                    <dt>{{ __('orders.buyer') }}</dt>
                    <dd class="mono">{{ $order['buyerId'] }}</dd>
                    <dt>{{ __('orders.created_at') }}</dt>
                    <dd>@datetime($order['createdAt'])</dd>
                    <dt>{{ __('orders.updated_at') }}</dt>
                    <dd>@datetime($order['updatedAt'])</dd>
                    <dt>{{ __('orders.mine') }}</dt>
                    <dd class="num fw-semibold">@money($order['sellerCents'])</dd>
                    <dt>{{ __('orders.total') }}</dt>
                    <dd class="num">@money($order['totalCents'])</dd>
                </dl>
            </x-card>
        </div>

        <div class="col-lg-8">
            <x-card :title="__('orders.items')" flush>
                <div class="table-responsive">
                    <table class="table panel-table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>{{ __('orders.item_product') }}</th>
                                <th>{{ __('orders.item_seller') }}</th>
                                <th class="text-end">{{ __('orders.item_quantity') }}</th>
                                <th class="text-end">{{ __('orders.item_unit_price') }}</th>
                                <th class="text-end">{{ __('orders.item_subtotal') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($order['items'] as $item)
                                @php $mine = $item['sellerId'] === $sellerId; @endphp
                                <tr @class(['is-muted' => ! $mine])>
                                    <td class="mono">{{ $item['productId'] }}</td>
                                    <td>{{ $mine ? __('orders.mine') : __('orders.other_seller') }}</td>
                                    <td class="text-end num">{{ $item['quantity'] }}</td>
                                    <td class="text-end num">@money($item['priceCents'])</td>
                                    <td class="text-end num">@money($item['quantity'] * $item['priceCents'])</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </x-card>
        </div>
    </div>
@endsection
