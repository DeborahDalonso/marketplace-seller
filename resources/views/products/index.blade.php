@extends('layouts.app')

@section('title', __('products.title'))

@section('content')
    <x-page-header :title="__('products.title')" :subtitle="__('products.subtitle')">
        <a href="{{ route('products.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-lg me-1"></i>{{ __('products.new') }}
        </a>
    </x-page-header>

    <x-card flush>
        @if ($products->isEmpty())
            <x-empty-state icon="box-seam" :title="__('products.empty_title')" :text="__('products.empty_text')">
                <a href="{{ route('products.create') }}" class="btn btn-outline-primary">{{ __('products.new') }}</a>
            </x-empty-state>
        @else
            <div class="table-responsive">
                <table class="table panel-table align-middle mb-0">
                    <thead>
                        <tr>
                            <th>{{ __('products.col_name') }}</th>
                            <th>{{ __('products.col_category') }}</th>
                            <th class="text-end">{{ __('products.col_price') }}</th>
                            <th class="text-end">{{ __('products.col_stock') }}</th>
                            <th>{{ __('products.col_status') }}</th>
                            <th class="text-end">{{ __('products.col_actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($products as $product)
                            <tr>
                                <td>
                                    <a href="{{ route('products.edit', $product['id']) }}" class="panel-table__primary">{{ $product['name'] }}</a>
                                    @if (! empty($product['description']))
                                        <small class="d-block text-muted text-truncate" style="max-width: 32ch">{{ $product['description'] }}</small>
                                    @endif
                                </td>
                                <td>{{ $product['categoryName'] ?? $product['categoryId'] ?? '' }}</td>
                                <td class="text-end num">@money($product['priceCents'] ?? 0)</td>
                                <td class="text-end num">
                                    @if (($product['stock'] ?? 0) > 0)
                                        {{ __('products.units', ['count' => $product['stock']]) }}
                                    @else
                                        <span class="text-danger">{{ __('products.stock_out') }}</span>
                                    @endif
                                </td>
                                <td><x-status-badge :status="($product['active'] ?? false) ? 'active' : 'inactive'" /></td>
                                <td class="text-end text-nowrap">
                                    <a href="{{ route('products.edit', $product['id']) }}" class="btn btn-sm btn-outline-secondary" title="{{ __('ui.action.edit') }}">
                                        <i class="bi bi-pencil"></i><span class="visually-hidden">{{ __('ui.action.edit') }}</span>
                                    </a>
                                    @if ($product['active'] ?? false)
                                        <form method="post" action="{{ route('products.destroy', $product['id']) }}" class="d-inline" data-confirm="{{ __('products.deactivate_confirm') }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-secondary" title="{{ __('products.deactivate') }}">
                                                <i class="bi bi-eye-slash"></i><span class="visually-hidden">{{ __('products.deactivate') }}</span>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @if ($products->hasPages())
                <div class="panel-table__footer">{{ $products->links() }}</div>
            @endif
        @endif
    </x-card>
@endsection
