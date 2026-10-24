<?php

namespace Tests\Feature\Admin;

use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class PromoTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Diskon Akhir Pekan',
            'code' => 'weekend10',
            'description' => 'Potongan 10 persen',
            'type' => 'persen',
            'value' => 10,
            'applies_to' => 'semua',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(7)->toDateString(),
            'min_spend' => 0,
        ], $overrides);
    }

    public function test_promo_index_loads(): void
    {
        Promo::factory()->create(['title' => 'Promo Mantap']);

        $this->actingAsAdmin()->get('/admin/promo')->assertOk()->assertSee('Promo Mantap');
    }

    public function test_admin_can_create_promo_and_code_is_uppercased(): void
    {
        $this->actingAsAdmin()->post('/admin/promo/store', $this->payload())->assertRedirect();

        $this->assertDatabaseHas('promo', ['title' => 'Diskon Akhir Pekan', 'code' => 'WEEKEND10']);
    }

    public function test_promo_can_be_created_without_code(): void
    {
        $this->actingAsAdmin()->post('/admin/promo/store', $this->payload(['code' => null]))->assertRedirect();

        $this->assertDatabaseHas('promo', ['title' => 'Diskon Akhir Pekan', 'code' => null]);
    }

    public function test_promo_code_must_be_unique(): void
    {
        Promo::factory()->create(['code' => 'WEEKEND10']);

        $this->actingAsAdmin()->post('/admin/promo/store', $this->payload())
            ->assertSessionHasErrors('code');
    }

    public function test_create_promo_validates_type_and_dates(): void
    {
        $this->actingAsAdmin()->post('/admin/promo/store', $this->payload([
            'type' => 'tidak_ada',
            'end_date' => now()->subDay()->toDateString(),
        ]))->assertSessionHasErrors(['type', 'end_date']);
    }

    public function test_admin_can_update_promo(): void
    {
        $promo = Promo::factory()->create(['title' => 'Lama', 'code' => 'LAMA']);

        $this->actingAsAdmin()->post("/admin/promo/{$promo->id}/update", $this->payload([
            'title' => 'Baru',
            'code' => 'baru',
        ]))->assertRedirect();

        $this->assertSame('Baru', $promo->fresh()->title);
        $this->assertSame('BARU', $promo->fresh()->code);
    }

    public function test_admin_can_toggle_promo_active(): void
    {
        $promo = Promo::factory()->create(['is_active' => true]);

        $this->actingAsAdmin()->post("/admin/promo/{$promo->id}/toggle-active")->assertRedirect();

        $this->assertFalse($promo->fresh()->is_active);
    }

    public function test_admin_can_delete_promo(): void
    {
        $promo = Promo::factory()->create();

        $this->actingAsAdmin()->delete("/admin/promo/{$promo->id}")->assertRedirect();

        $this->assertDatabaseMissing('promo', ['id' => $promo->id]);
    }
}
