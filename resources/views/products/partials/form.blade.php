{{-- Campos compartilhados por criar e editar. $product vem null na criação. --}}
<div class="row g-3">
    <div class="col-md-8">
        <label for="name" class="form-label">{{ __('products.name') }}</label>
        <input type="text" name="name" id="name" value="{{ old('name', $product['name'] ?? '') }}" required maxlength="120"
               class="form-control @error('name') is-invalid @enderror">
        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="category_id" class="form-label">{{ __('products.category') }}</label>
        <select name="category_id" id="category_id" required class="form-select @error('category_id') is-invalid @enderror">
            <option value="">{{ __('products.category_placeholder') }}</option>
            @foreach ($categories as $category)
                <option value="{{ $category['id'] }}" @selected(old('category_id', $product['categoryId'] ?? '') === $category['id'])>
                    {{ $category['name'] }}
                </option>
            @endforeach
        </select>
        @error('category_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="price" class="form-label">{{ __('products.price') }}</label>
        <input type="number" name="price" id="price" step="0.01" min="0" inputmode="decimal" required
               value="{{ old('price', isset($product['priceCents']) ? number_format($product['priceCents'] / 100, 2, '.', '') : '') }}"
               class="form-control num @error('price') is-invalid @enderror">
        @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4">
        <label for="stock" class="form-label">{{ __('products.stock') }}</label>
        <input type="number" name="stock" id="stock" step="1" min="0" inputmode="numeric" required
               value="{{ old('stock', $product['stock'] ?? 0) }}"
               class="form-control num @error('stock') is-invalid @enderror">
        @error('stock')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>

    <div class="col-md-4 d-flex align-items-end">
        <div class="form-check form-switch mb-2">
            <input type="checkbox" name="active" id="active" value="1" class="form-check-input"
                   @checked(old('active', $product['active'] ?? true))>
            <label for="active" class="form-check-label">{{ __('products.active') }}</label>
            <div class="form-text">{{ __('products.active_hint') }}</div>
        </div>
    </div>

    <div class="col-12">
        <label for="description" class="form-label">{{ __('products.description') }}</label>
        <textarea name="description" id="description" rows="4" maxlength="2000"
                  class="form-control @error('description') is-invalid @enderror">{{ old('description', $product['description'] ?? '') }}</textarea>
        @error('description')<div class="invalid-feedback">{{ $message }}</div>@enderror
    </div>
</div>

<div class="form-actions">
    <button type="submit" class="btn btn-primary">{{ __('ui.action.save') }}</button>
    <a href="{{ route('products.index') }}" class="btn btn-link">{{ __('ui.action.cancel') }}</a>
</div>
