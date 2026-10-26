<?php

namespace Tests\Feature;

use App\Models\Store;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class StoreTest extends TestCase
{
    use RefreshDatabase;

    // PNG de 1x1 pixel: a regra "image" olha o conteúdo, não a extensão
    private const PNG_1X1 = 'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg==';

    private function validData(array $overrides = []): array
    {
        return $overrides + [
            'name' => 'Casa da Ana',
            'slug' => 'casa-da-ana',
            'description' => 'Cerâmica feita à mão.',
            'pix_key' => 'ana@example.com',
        ];
    }

    public function test_primeira_entrada_mostra_formulario_vazio(): void
    {
        $this->actingAsSeller()
            ->get('/store')
            ->assertOk()
            ->assertSee(__('store.subtitle_new'))
            ->assertDontSee(__('ui.action.cancel'));
    }

    public function test_cria_a_loja_do_vendedor(): void
    {
        $this->actingAsSeller()
            ->put('/store', $this->validData())
            ->assertRedirect('/store')
            ->assertSessionHas('success', __('store.created'));

        $this->assertDatabaseHas('stores', [
            'seller_id' => self::SELLER_ID,
            'slug' => 'casa-da-ana',
            'pix_key' => 'ana@example.com',
        ]);
    }

    public function test_atualiza_a_loja_existente_sem_duplicar(): void
    {
        Store::factory()->create(['seller_id' => self::SELLER_ID, 'name' => 'Antigo']);

        $this->actingAsSeller()
            ->put('/store', $this->validData(['name' => 'Novo nome']))
            ->assertRedirect('/store')
            ->assertSessionHas('success', __('store.updated'));

        $this->assertSame(1, Store::count());
        $this->assertSame('Novo nome', Store::first()->name);
    }

    public function test_formulario_de_edicao_vem_preenchido(): void
    {
        Store::factory()->create(['seller_id' => self::SELLER_ID, 'name' => 'Casa da Ana', 'slug' => 'casa-da-ana']);

        $this->actingAsSeller()
            ->get('/store')
            ->assertOk()
            ->assertSee('value="Casa da Ana"', false)
            ->assertSee('value="casa-da-ana"', false);
    }

    public function test_salva_o_logo_no_disco_publico(): void
    {
        Storage::fake('public');
        $logo = UploadedFile::fake()->createWithContent('logo.png', base64_decode(self::PNG_1X1));

        $this->actingAsSeller()
            ->put('/store', $this->validData(['logo' => $logo]))
            ->assertRedirect('/store');

        $path = Store::first()->logo_path;

        $this->assertStringStartsWith('logos/', $path);
        Storage::disk('public')->assertExists($path);
    }

    public function test_troca_de_logo_apaga_o_anterior(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('logos/antigo.png', 'x');
        Store::factory()->create(['seller_id' => self::SELLER_ID, 'logo_path' => 'logos/antigo.png']);

        $logo = UploadedFile::fake()->createWithContent('novo.png', base64_decode(self::PNG_1X1));

        $this->actingAsSeller()->put('/store', $this->validData(['logo' => $logo]));

        Storage::disk('public')->assertMissing('logos/antigo.png');
        Storage::disk('public')->assertExists(Store::first()->logo_path);
    }

    public function test_slug_de_outra_loja_e_rejeitado(): void
    {
        Store::factory()->create(['slug' => 'casa-da-ana']);

        $this->actingAsSeller()
            ->from('/store')
            ->put('/store', $this->validData())
            ->assertRedirect('/store')
            ->assertSessionHasErrors('slug');

        $this->assertDatabaseMissing('stores', ['seller_id' => self::SELLER_ID]);
    }

    public function test_proprio_slug_continua_valido_ao_editar(): void
    {
        Store::factory()->create(['seller_id' => self::SELLER_ID, 'slug' => 'casa-da-ana']);

        $this->actingAsSeller()
            ->put('/store', $this->validData())
            ->assertSessionHasNoErrors();
    }

    public function test_campos_obrigatorios_voltam_com_erro(): void
    {
        $this->actingAsSeller()
            ->from('/store')
            ->put('/store', ['name' => '', 'slug' => 'com espaço', 'pix_key' => ''])
            ->assertRedirect('/store')
            ->assertSessionHasErrors(['name', 'slug', 'pix_key']);
    }

    public function test_nao_mexe_na_loja_de_outro_vendedor(): void
    {
        $other = Store::factory()->create(['name' => 'Loja alheia']);

        $this->actingAsSeller()->put('/store', $this->validData());

        $this->assertSame('Loja alheia', $other->fresh()->name);
        $this->assertSame(2, Store::count());
    }
}
