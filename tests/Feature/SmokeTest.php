<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Table;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    public function test_migrations_and_factories_work(): void
    {
        $admin = User::factory()->admin()->create();
        $category = Category::factory()->create();
        $menu = Menu::factory()->for($category)->create();
        $table = Table::factory()->create();
        $order = Order::factory()->create();

        $this->assertTrue($admin->isAdmin());
        $this->assertSame('admin', $admin->primaryRole());
        $this->assertDatabaseHas('menu', ['id' => $menu->id]);
        $this->assertDatabaseHas('meja', ['id' => $table->id]);
        $this->assertDatabaseHas('pesanan', ['id' => $order->id]);
    }
}
