<?php

namespace Tests\Feature\Owner;

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

    public function test_owner_can_create_promo(): void
    {
        $this->actingAsOwner()->post('/owner/promo/store', $this->payload())->assertRedirect();

        $this->assertDatabaseHas('promo', ['title' => 'Promo Owner', 'code' => 'OWNER10', 'is_active' => true]);
    }

    public function test_owner_promo_code_must_be_unique(): void
    {
        Promo::factory()->create(['code' => 'OWNER10']);

        $this->actingAsOwner()->post('/owner/promo/store', $this->payload())
            ->assertSessionHasErrors('code');
    }

    public function test_owner_promo_validates_required_fields(): void
    {
        $this->actingAsOwner()->post('/owner/promo/store', [])
            ->assertSessionHasErrors(['title', 'description', 'type', 'value', 'applies_to', 'start_date', 'end_date']);
    }

    public function test_owner_can_update_promo(): void
    {
        $promo = Promo::factory()->create(['title' => 'Lama']);

        $this->actingAsOwner()->post("/owner/promo/{$promo->id}/update", $this->payload(['title' => 'Baru']))
            ->assertRedirect();

        $this->assertSame('Baru', $promo->fresh()->title);
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
}
