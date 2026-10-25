<?php

namespace Tests\Feature\Admin;

use App\Models\Category;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VoucherTest extends TestCase
{
    use RefreshDatabase;

    private function payload(array $overrides = []): array
    {
        return array_merge([
            'title' => 'Voucher Admin',
            'code' => 'admin10',
            'description' => 'Diskon voucher admin',
            'type' => 'persen',
            'value' => 10,
            'applies_to' => 'semua',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addDays(14)->toDateString(),
            'is_active' => '1',
            'min_spend' => 0,
        ], $overrides);
    }

    public function test_admin_can_view_create_voucher_page(): void
    {
        $response = $this->actingAsAdmin()->get(route('admin.vouchers.create'));

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
            ->assertSee(route('admin.vouchers.index'));
    }

    public function test_admin_can_view_edit_voucher_page(): void
    {
        $promo = Promo::factory()->create([
            'title' => 'Voucher Kopi Mantap',
            'code' => 'KOPIMANTAP',
        ]);

        $response = $this->actingAsAdmin()->get(route('admin.vouchers.edit', $promo->id));

        $response->assertOk()
            ->assertSee('Ubah Promo')
            ->assertSee('Voucher Kopi Mantap')
            ->assertSee('KOPIMANTAP')
            ->assertSee('Simpan Promo')
            ->assertSee('Batal')
            ->assertDontSee('Tentukan nilai diskon, masa berlaku, dan target menu kafe')
            ->assertDontSee('text-cafe-muted mb-3')
            ->assertSee('id="form-back-btn"', false)
            ->assertSee(route('admin.vouchers.index'));
    }

    public function test_admin_edit_voucher_returns_404_for_missing_id(): void
    {
        $this->actingAsAdmin()->get('/admin/promo-voucher/999999/ubah')->assertNotFound();
    }

    public function test_non_admin_cannot_access_admin_voucher_pages(): void
    {
        $promo = Promo::factory()->create();

        $this->actingAsKasir()->get(route('admin.vouchers.create'))->assertRedirect();
        $this->actingAsKasir()->get(route('admin.vouchers.edit', $promo->id))->assertRedirect();
    }

    public function test_admin_can_create_voucher_and_it_redirects_to_index_with_log(): void
    {
        $response = $this->actingAsAdmin()->post(route('admin.vouchers.store'), $this->payload([
            'title' => 'Voucher Spesial Admin',
            'code' => 'spesial10',
        ]));

        $response->assertRedirect(route('admin.vouchers.index'));
        $this->assertDatabaseHas('promo', [
            'title' => 'Voucher Spesial Admin',
            'code' => 'SPESIAL10',
            'is_active' => true,
        ]);
        $this->assertDatabaseHas('log_aktivitas', [
            'activity' => 'Membuat promo baru: Voucher Spesial Admin',
            'activity_type' => 'promo',
        ]);
    }

    public function test_admin_voucher_code_must_be_unique(): void
    {
        Promo::factory()->create(['code' => 'ADMIN10']);

        $this->actingAsAdmin()->post(route('admin.vouchers.store'), $this->payload())
            ->assertSessionHasErrors('code');
    }

    public function test_admin_voucher_validates_required_fields(): void
    {
        $this->actingAsAdmin()->post(route('admin.vouchers.store'), [])
            ->assertSessionHasErrors(['title', 'description', 'type', 'value', 'applies_to', 'start_date', 'end_date']);
    }

    public function test_admin_target_category_only_required_when_applies_to_category(): void
    {
        $payloadNoCategory = $this->payload(['applies_to' => 'kategori', 'target_category_id' => null]);
        $this->actingAsAdmin()->post(route('admin.vouchers.store'), $payloadNoCategory)
            ->assertSessionHasErrors('target_category_id');

        $category = Category::factory()->create();
        $payloadWithCategory = $this->payload(['applies_to' => 'kategori', 'target_category_id' => $category->id]);
        $this->actingAsAdmin()->post(route('admin.vouchers.store'), $payloadWithCategory)
            ->assertRedirect(route('admin.vouchers.index'));
    }

    public function test_admin_can_update_voucher_and_it_redirects_to_index_with_log(): void
    {
        $promo = Promo::factory()->create(['title' => 'Voucher Lama', 'code' => 'LAMA']);

        $response = $this->actingAsAdmin()->post(route('admin.vouchers.update', $promo->id), $this->payload([
            'title' => 'Voucher Diperbarui',
            'code' => 'baru',
        ]));

        $response->assertRedirect(route('admin.vouchers.index'));
        $this->assertSame('Voucher Diperbarui', $promo->fresh()->title);
        $this->assertSame('BARU', $promo->fresh()->code);
        $this->assertDatabaseHas('log_aktivitas', [
            'activity' => 'Memperbarui promo: Voucher Diperbarui',
            'activity_type' => 'promo',
        ]);
    }

    public function test_admin_voucher_index_has_links_and_no_modal_markup(): void
    {
        $promo = Promo::factory()->create(['title' => 'Promo Cek Markup Admin']);

        $response = $this->actingAsAdmin()->get(route('admin.vouchers.index'));

        $response->assertOk()
            ->assertSee(route('admin.vouchers.create'))
            ->assertSee(route('admin.vouchers.edit', $promo->id))
            ->assertDontSee('id="promo-modal"', false)
            ->assertDontSee('openPromoModal', false)
            ->assertDontSee('editPromoModal', false)
            ->assertDontSee('closePromoModal', false);
    }
}
