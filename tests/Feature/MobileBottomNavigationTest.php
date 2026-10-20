<?php

namespace Tests\Feature;

use App\Models\Order;
use App\Models\Promo;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class MobileBottomNavigationTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_bottom_navigation_has_five_items_and_expected_more_sheet(): void
    {
        if (config('features.legacy_admin')) {
            $this->markTestSkipped('The active-admin navigation assertion applies when FEATURE_LEGACY_ADMIN is disabled.');
        }

        $this->actingAsAdmin();

        $html = $this->load(route('admin.dashboard'));
        $navHtml = $this->regionHtml($html, 'mobile-bottom-nav');

        $this->assertSame(
            ['Beranda', 'Menu', 'Staf', 'Promo', 'Lainnya'],
            $this->labelsWithin($html, 'mobile-bottom-nav'),
        );
        $this->assertCount(5, $this->itemsWithin($html, 'mobile-bottom-nav'));
        $this->assertStringContainsString('md:hidden', $navHtml);
        $this->assertStringContainsString('env(safe-area-inset-bottom)', $navHtml);
        $this->assertStringContainsString('bg-[#D4954D] text-[#290024]', $navHtml);
        $this->assertStringContainsString('text-[#775533]', $navHtml);

        foreach ($this->itemsWithin($html, 'mobile-bottom-nav') as $item) {
            $this->assertStringContainsString('min-h-[48px]', $item->getAttribute('class'));
        }

        $sheet = $this->regionHtml($html, 'mobile-more-sheet');
        $sheetLabels = $this->labelsWithin($html, 'mobile-more-sheet');

        foreach ([
            'Meja & Metode Pembayaran',
            'Pengaturan Website',
            'Log Aktivitas',
            'Profil Saya',
            'Keluar',
        ] as $label) {
            $this->assertContains($label, $sheetLabels);
        }

        $this->assertStringContainsString('action="'.route('admin.logout').'"', $sheet);
        $this->assertStringNotContainsString('/admin/pesanan', $sheet);
        $this->assertStringNotContainsString('/admin/kategori', $sheet);
    }

    public function test_admin_active_item_tracks_dashboard_and_subpages(): void
    {
        $this->actingAsAdmin();

        $dashboard = $this->activeNavItem($this->load(route('admin.dashboard')));
        $this->assertNotNull($dashboard);
        $this->assertSame(route('admin.dashboard'), $dashboard->getAttribute('href'));

        $catalog = $this->activeNavItem($this->load(route('admin.catalog.index')));
        $this->assertNotNull($catalog);
        $this->assertSame(route('admin.catalog.index'), $catalog->getAttribute('href'));

        $website = $this->activeNavItem($this->load(route('admin.website.index')));
        $this->assertNotNull($website);
        $this->assertSame('mobile-more-button', $website->getAttribute('id'));
    }

    public function test_admin_navigation_links_are_reachable(): void
    {
        $this->actingAsAdmin();

        $this->assertNavLinksResolve($this->load(route('admin.dashboard')));
    }

    public function test_owner_bottom_navigation_only_contains_owner_items(): void
    {
        Promo::factory()->create(['is_active' => true]);

        $this->actingAsOwner();

        $html = $this->load(route('owner.dashboard'));

        $this->assertSame(
            ['Beranda', 'Laporan', 'Kasir', 'Promo', 'Lainnya'],
            $this->labelsWithin($html, 'mobile-bottom-nav'),
        );
        $this->assertCount(5, $this->itemsWithin($html, 'mobile-bottom-nav'));

        $labels = $this->labelsWithin($html, 'mobile-bottom-nav');
        $this->assertNotContains('Menu', $labels);
        $this->assertNotContains('Staf', $labels);
        $this->assertNotContains('POS', $labels);
        $this->assertNotContains('Pesanan', $labels);

        $promo = $this->navItemByLabel($html, 'Promo');
        $this->assertNotNull($promo);
        $this->assertSame('1', $this->badgeWithin($promo));

        $sheet = $this->regionHtml($html, 'mobile-more-sheet');
        $sheetLabels = $this->labelsWithin($html, 'mobile-more-sheet');

        foreach (['Pengaturan Bisnis', 'Profil Saya', 'Keluar'] as $label) {
            $this->assertContains($label, $sheetLabels);
        }

        $this->assertStringNotContainsString('Manajemen User Staf', $sheet);
        $this->assertStringNotContainsString('Log Aktivitas', $sheet);
    }

    public function test_owner_active_item_tracks_dashboard_and_subpages(): void
    {
        $this->actingAsOwner();

        $dashboard = $this->activeNavItem($this->load(route('owner.dashboard')));
        $this->assertNotNull($dashboard);
        $this->assertSame(route('owner.dashboard'), $dashboard->getAttribute('href'));

        $reports = $this->activeNavItem($this->load(route('owner.reports.index')));
        $this->assertNotNull($reports);
        $this->assertSame(route('owner.reports.index'), $reports->getAttribute('href'));

        $settings = $this->activeNavItem($this->load(route('owner.settings.index')));
        $this->assertNotNull($settings);
        $this->assertSame('mobile-more-button', $settings->getAttribute('id'));
    }

    public function test_owner_navigation_links_are_reachable(): void
    {
        $this->actingAsOwner();

        $this->assertNavLinksResolve($this->load(route('owner.dashboard')));
    }

    public function test_kasir_bottom_navigation_has_new_order_badge_and_expected_more_sheet(): void
    {
        $this->actingAsKasir();
        $this->openShift();
        Order::factory()->web()->create();

        $html = $this->load(route('kasir.dashboard'));

        $this->assertSame(
            ['Beranda', 'POS', 'Pesanan', 'Meja', 'Lainnya'],
            $this->labelsWithin($html, 'mobile-bottom-nav'),
        );
        $this->assertCount(5, $this->itemsWithin($html, 'mobile-bottom-nav'));

        $labels = $this->labelsWithin($html, 'mobile-bottom-nav');
        $this->assertNotContains('Menu', $labels);
        $this->assertNotContains('Laporan', $labels);
        $this->assertNotContains('Promo', $labels);

        $orders = $this->navItemByLabel($html, 'Pesanan');
        $this->assertNotNull($orders);
        $this->assertSame('1', $this->badgeWithin($orders));

        $sheet = $this->regionHtml($html, 'mobile-more-sheet');
        $sheetLabels = $this->labelsWithin($html, 'mobile-more-sheet');

        foreach ([
            'Pembayaran & Struk',
            'Buka/Tutup Shift & Rekap Kas',
            'Profil Saya',
            'Keluar',
        ] as $label) {
            $this->assertContains($label, $sheetLabels);
        }

        $this->assertStringNotContainsString('Manajemen User Staf', $sheet);
        $this->assertStringNotContainsString('Dashboard Bisnis', $sheet);
    }

    public function test_kasir_active_item_tracks_dashboard_and_subpages(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $dashboard = $this->activeNavItem($this->load(route('kasir.dashboard')));
        $this->assertNotNull($dashboard);
        $this->assertSame(route('kasir.dashboard'), $dashboard->getAttribute('href'));

        $orders = $this->activeNavItem($this->load(route('kasir.orders.index')));
        $this->assertNotNull($orders);
        $this->assertSame(route('kasir.orders.index'), $orders->getAttribute('href'));

        $payments = $this->activeNavItem($this->load(route('kasir.payments.index')));
        $this->assertNotNull($payments);
        $this->assertSame('mobile-more-button', $payments->getAttribute('id'));
    }

    public function test_kasir_navigation_links_are_reachable(): void
    {
        $this->actingAsKasir();
        $this->openShift();

        $this->assertNavLinksResolve($this->load(route('kasir.dashboard')));
    }

    public function test_bottom_navigation_is_hidden_on_receipt_and_print_screens(): void
    {
        $this->get('/login')
            ->assertOk()
            ->assertDontSee('id="mobile-bottom-nav"', false);

        $this->actingAsKasir();
        $this->openShift();
        $order = Order::factory()->paid()->create();

        $receipt = $this->get(route('kasir.payments.receipt', $order))
            ->assertOk()
            ->assertDontSee('id="mobile-bottom-nav"', false)
            ->getContent();

        $this->assertStringContainsString('aria-label="Kembali"', $receipt);
        $this->assertStringContainsString(route('kasir.dashboard'), $receipt);

        $this->actingAsOwner()
            ->get(route('owner.reports.print'))
            ->assertOk()
            ->assertDontSee('id="mobile-bottom-nav"', false);
    }

    #[Group('legacy')]
    public function test_legacy_admin_items_are_kept_inside_more_sheet(): void
    {
        if (! config('features.legacy_admin')) {
            $this->markTestSkipped('Only applies when FEATURE_LEGACY_ADMIN is enabled.');
        }

        $this->actingAsAdmin();

        $html = $this->load(route('admin.dashboard'));
        $sheet = $this->regionHtml($html, 'mobile-more-sheet');
        $sheetLabels = $this->labelsWithin($html, 'mobile-more-sheet');

        foreach ([
            'Pesanan',
            'Kategori',
            'Stok',
            'Pengguna',
            'Meja',
            'Pengaturan',
        ] as $label) {
            $this->assertContains($label, $sheetLabels);
        }

        $this->assertStringContainsString('/admin/pesanan', $sheet);
        $this->assertStringContainsString('/admin/kategori', $sheet);
        $this->assertCount(5, $this->itemsWithin($html, 'mobile-bottom-nav'));
    }

    private function load(string $url): string
    {
        return $this->get($url)->assertOk()->getContent();
    }

    private function document(string $html): \DOMDocument
    {
        $document = new \DOMDocument;
        libxml_use_internal_errors(true);
        $document->loadHTML($html);
        libxml_clear_errors();

        return $document;
    }

    private function xpath(string $html): \DOMXPath
    {
        return new \DOMXPath($this->document($html));
    }

    /**
     * @return array<int, \DOMElement>
     */
    private function itemsWithin(string $html, string $regionId): array
    {
        $nodes = $this->xpath($html)->query(
            "//*[@id=\"{$regionId}\"]//*[@data-mobile-nav-item]"
        );

        $items = [];

        foreach ($nodes as $node) {
            if ($node instanceof \DOMElement) {
                $items[] = $node;
            }
        }

        return $items;
    }

    /**
     * @return array<int, string>
     */
    private function labelsWithin(string $html, string $regionId): array
    {
        $nodes = $this->xpath($html)->query(
            "//*[@id=\"{$regionId}\"]//*[@data-nav-label]"
        );

        $labels = [];

        foreach ($nodes as $node) {
            $labels[] = trim(preg_replace('/\s+/', ' ', $node->textContent));
        }

        return $labels;
    }

    private function navItemByLabel(string $html, string $label): ?\DOMElement
    {
        $document = $this->document($html);
        $xpath = new \DOMXPath($document);
        $nodes = $xpath->query('//*[@id="mobile-bottom-nav"]//*[@data-mobile-nav-item]');

        foreach ($nodes as $item) {
            if (! $item instanceof \DOMElement) {
                continue;
            }

            $labelNode = $xpath->query('.//*[@data-nav-label]', $item)->item(0);

            if ($labelNode && trim($labelNode->textContent) === $label) {
                return $item;
            }
        }

        return null;
    }

    private function badgeWithin(\DOMElement $item): string
    {
        $document = $item->ownerDocument;
        $badge = (new \DOMXPath($document))->query('.//*[@data-nav-badge]', $item)->item(0);

        return $badge ? trim($badge->textContent) : '';
    }

    private function activeNavItem(string $html): ?\DOMElement
    {
        $node = $this->xpath($html)
            ->query('//*[@id="mobile-bottom-nav"]//*[@aria-current="page"]')
            ->item(0);

        return $node instanceof \DOMElement ? $node : null;
    }

    private function regionHtml(string $html, string $regionId): string
    {
        $document = $this->document($html);
        $node = (new \DOMXPath($document))->query("//*[@id=\"{$regionId}\"]")->item(0);

        $this->assertNotNull($node);

        return $document->saveHTML($node);
    }

    private function assertNavLinksResolve(string $html): void
    {
        $nodes = $this->xpath($html)->query(
            '//*[@id="mobile-bottom-nav"]//a[@data-mobile-nav-item]'
            .' | //*[@id="mobile-more-sheet"]//a[@data-mobile-nav-item]'
        );

        $urls = [];

        foreach ($nodes as $node) {
            if ($node instanceof \DOMElement) {
                $urls[] = $node->getAttribute('href');
            }
        }

        $this->assertNotEmpty($urls);

        foreach (array_unique($urls) as $url) {
            $path = parse_url($url, PHP_URL_PATH);
            $query = parse_url($url, PHP_URL_QUERY);

            $this->get($path.($query ? '?'.$query : ''))->assertOk();
        }
    }
}
