<?php

namespace App\Services;

use App\Models\Store;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StoreService
{
    public function findForSeller(string $sellerId): ?Store
    {
        return Store::where('seller_id', $sellerId)->first();
    }

    /**
     * Cria ou atualiza a loja do vendedor. Cada vendedor tem uma loja só,
     * por isso a chave é o seller_id e não o id da linha.
     */
    public function save(string $sellerId, array $data, ?UploadedFile $logo = null): Store
    {
        $store = Store::firstOrNew(['seller_id' => $sellerId]);
        $store->fill($data);

        if ($logo) {
            if ($store->logo_path) {
                Storage::disk('public')->delete($store->logo_path);
            }
            $store->logo_path = $logo->store('logos', 'public');
        }

        $store->save();

        Log::info('[StoreService] loja salva', ['sellerId' => $sellerId, 'storeId' => $store->id]);

        return $store;
    }
}
