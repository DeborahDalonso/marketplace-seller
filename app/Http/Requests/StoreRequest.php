<?php

namespace App\Http\Requests;

use App\Models\Store;
use App\Services\SellerSession;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(SellerSession $session): array
    {
        $ownStoreId = Store::where('seller_id', $session->sellerId())->value('id');

        return [
            'name' => ['required', 'string', 'max:120'],
            'slug' => ['required', 'string', 'max:120', 'alpha_dash', Rule::unique('stores', 'slug')->ignore($ownStoreId)],
            'description' => ['nullable', 'string', 'max:2000'],
            'pix_key' => ['required', 'string', 'max:140'],
            'logo' => ['nullable', 'image', 'max:2048'],
        ];
    }
}
