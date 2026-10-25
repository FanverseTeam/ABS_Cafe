<?php

namespace Tests\Feature\Owner;

use App\Models\Category;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PromoTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Promo Owner',
            'code' => 'owner10',
            'description' => 'Diskon owner',
            'type' => 'persen',
            'value' => 10,
            'applies_to' => 'semua',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(10)->toDateString(),
            'is_active' => '1',
            'min_spend' => 0,
        ], $overrides);
    }

    public function test_owner_promo_index_loads(): void
    {
        Promo::factory()->create(['title' => 'Promo Owner List']);

        $this->actingAsOwner()->get('/owner/promo')->assertOk()->assertSee('Promo Owner List');
    }

    public function test_owner_can_view_create_promo_page(): void
    {
        $response = $this->actingAsOwner()->get(route('owner.promos.create'));

        $response->assertOk()
            ->assertSee('Buat Promo Baru')
            ->assertSee('Informasi Promo')
            ->assertSee('Diskon')
            ->assertSee('Periode')
            ->assertSee('Tampilan')
            ->assertSee('Status')
            ->assertSee('Simpan Promo')
            ->assertSee('Batal')
            ->assertDontSee('Tentukan nilai diskon, masa berlaku, dan target menu kafe')
            ->assertDontSee('text-cafe-muted mb-3')
            ->assertSee('id="form-back-btn"', false)
            ->assertSee(route('owner.promos.index'));
    }

    public function test_owner_can_view_edit_promo_page(): void
    {
        $promo = Promo::factory()->create(['title' => 'Promo Diskon Hebat', 'code' => 'HEBAT20']);

        $response = $this->actingAsOwner()->get(route('owner.promos.edit', $promo->id));

        $response->assertOk()
            ->assertSee('Ubah Promo')
            ->assertSee('Promo Diskon Hebat')
            ->assertSee('HEBAT20')
            ->assertSee('Simpan Promo')
            ->assertDontSee('Tentukan nilai diskon, masa berlaku, dan target menu kafe')
            ->assertDontSee('text-cafe-muted mb-3')
            ->assertSee('id="form-back-btn"', false)
            ->assertSee(route('owner.promos.index'));
    }

    public function test_owner_edit_promo_returns_404_for_missing_id(): void
    {
        $this->actingAsOwner()->get('/owner/promo/999999/ubah')->assertNotFound();
    }

    public function test_non_owner_forbidden_from_promo_form_pages(): void
    {
        $promo = Promo::factory()->create();

        $this->actingAsKasir()->get(route('owner.promos.create'))->assertForbidden();
        $this->actingAsKasir()->get(route('owner.promos.edit', $promo->id))->assertForbidden();
    }

    public function test_owner_can_create_promo(): void
    {
        $response = $this->actingAsOwner()->post(route('owner.promos.store'), $this->payload());

        $response->assertRedirect(route('owner.promos.index'));
        $this->assertDatabaseHas('promo', ['title' => 'Promo Owner', 'code' => 'OWNER10', 'is_active' => true]);
        $this->assertDatabaseHas('log_aktivitas', ['activity' => 'Membuat promo baru: Promo Owner', 'activity_type' => 'promo']);
    }

    public function test_owner_promo_code_must_be_unique(): void
    {
        Promo::factory()->create(['code' => 'OWNER10']);

        $this->actingAsOwner()->post(route('owner.promos.store'), $this->payload())
            ->assertSessionHasErrors('code');
    }

    public function test_owner_promo_validates_required_fields(): void
    {
        $this->actingAsOwner()->post(route('owner.promos.store'), [])
            ->assertSessionHasErrors(['title', 'description', 'type', 'value', 'applies_to', 'start_date', 'end_date']);
    }

    public function test_owner_target_category_only_required_when_applies_to_category(): void
    {
        $payloadNoCategory = $this->payload(['applies_to' => 'kategori', 'target_category_id' => null]);
        $this->actingAsOwner()->post(route('owner.promos.store'), $payloadNoCategory)
            ->assertSessionHasErrors('target_category_id');

        $category = Category::factory()->create();
        $payloadWithCategory = $this->payload(['applies_to' => 'kategori', 'target_category_id' => $category->id]);
        $this->actingAsOwner()->post(route('owner.promos.store'), $payloadWithCategory)
            ->assertRedirect(route('owner.promos.index'));
    }

    public function test_owner_can_update_promo(): void
    {
        $promo = Promo::factory()->create(['title' => 'Lama']);

        $response = $this->actingAsOwner()->post(route('owner.promos.update', $promo->id), $this->payload(['title' => 'Baru']));

        $response->assertRedirect(route('owner.promos.index'));
        $this->assertSame('Baru', $promo->fresh()->title);
        $this->assertDatabaseHas('log_aktivitas', ['activity' => 'Memperbarui promo: Baru', 'activity_type' => 'promo']);
    }

    public function test_owner_can_toggle_promo(): void
    {
        $promo = Promo::factory()->create(['is_active' => true]);

        $this->actingAsOwner()->post("/owner/promo/{$promo->id}/toggle-active")->assertRedirect();

        $this->assertFalse($promo->fresh()->is_active);
    }

    public function test_owner_can_delete_promo(): void
    {
        $promo = Promo::factory()->create();

        $this->actingAsOwner()->delete("/owner/promo/{$promo->id}")->assertRedirect();

        $this->assertDatabaseMissing('promo', ['id' => $promo->id]);
    }

    public function test_owner_promo_index_has_links_and_no_modal_markup(): void
    {
        $promo = Promo::factory()->create(['title' => 'Promo Cek Markup']);

        $response = $this->actingAsOwner()->get(route('owner.promos.index'));

        $response->assertOk()
            ->assertSee(route('owner.promos.create'))
            ->assertSee(route('owner.promos.edit', $promo->id))
            ->assertDontSee('id="promo-modal"', false)
            ->assertDontSee('openPromoModal', false)
            ->assertDontSee('closePromoModal', false);
    }
}
