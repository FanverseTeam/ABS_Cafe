<?php

namespace Tests\Feature\Admin;

use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\Concerns\RequiresLegacyAdmin;
use Tests\TestCase;

#[Group('legacy')]
class ActivityLogTest extends TestCase
{
    use RefreshDatabase;
    use RequiresLegacyAdmin;

    private function makeLog(array $overrides = []): ActivityLog
    {
        return ActivityLog::create(array_merge([
            'user_name' => 'Admin ABS',
            'role' => 'admin',
            'activity' => 'Menambahkan menu baru',
            'activity_type' => 'menu',
            'ip_address' => '127.0.0.1',
        ], $overrides));
    }

    public function test_log_index_loads(): void
    {
        $this->makeLog(['activity' => 'Aktivitas Unik Sekali']);

        $this->actingAsAdmin()->get('/admin/log')->assertOk()->assertSee('Aktivitas Unik Sekali');
    }

    public function test_log_search_filters_by_activity_or_user(): void
    {
        $this->makeLog(['activity' => 'Menghapus promo lama', 'user_name' => 'Andi']);
        $this->makeLog(['activity' => 'Menambah meja', 'user_name' => 'Budi']);

        $this->actingAsAdmin()->get('/admin/log?search=Menghapus')
            ->assertOk()->assertSee('Menghapus promo lama')->assertDontSee('Menambah meja');
    }

    public function test_log_filters_by_type(): void
    {
        $this->makeLog(['activity' => 'Log menu', 'activity_type' => 'menu']);
        $this->makeLog(['activity' => 'Log promo', 'activity_type' => 'promo']);

        $this->actingAsAdmin()->get('/admin/log?type=promo')
            ->assertOk()->assertSee('Log promo')->assertDontSee('Log menu');
    }

    public function test_log_filters_by_user_name(): void
    {
        $this->makeLog(['activity' => 'Kerja Andi', 'user_name' => 'Andi']);
        $this->makeLog(['activity' => 'Kerja Budi', 'user_name' => 'Budi']);

        $this->actingAsAdmin()->get('/admin/log?user=Andi')
            ->assertOk()->assertSee('Kerja Andi')->assertDontSee('Kerja Budi');
    }

    public function test_logs_can_be_exported_as_csv(): void
    {
        $this->makeLog();

        $response = $this->actingAsAdmin()->get('/admin/log/export');

        $response->assertOk();
        $this->assertStringContainsString('text/csv', $response->headers->get('content-type'));
    }
}
