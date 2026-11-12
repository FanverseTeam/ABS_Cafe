<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Flatten menu_varian into menu table.
 *
 * Before: menu has many rows in menu_varian (name, price per variant).
 * After:  each menu row IS already the specific product (Latte Ice, Latte Hot).
 *         Two new nullable columns group them visually:
 *           - variant_group: display group name, e.g. 'Latte'
 *           - variant_label: pill label, e.g. 'Ice' or 'Hot'
 *         Prices were already on each menu row; variant prices overwrite menu.price.
 *         Drops: menu_varian table, variant_id columns on menu & pesanan_item.
 */
return new class extends Migration
{
    public function up(): void
    {
        // 1. Add grouping columns to menu
        if (Schema::hasTable('menu')) {
            Schema::table('menu', function (Blueprint $table) {
                if (! Schema::hasColumn('menu', 'variant_group')) {
                    $table->string('variant_group')->nullable()->after('name');
                }
                if (! Schema::hasColumn('menu', 'variant_label')) {
                    $table->string('variant_label')->nullable()->after('variant_group');
                }
            });
        }

        // 2. Migrate data: extract variant_group and variant_label from menu name
        //    Pattern: "Name (Label)" → group='Name', label='Label'
        //    Also copy the variant price (from menu_varian) into menu.price
        if (Schema::hasTable('menu_varian')) {
            $variants = DB::table('menu_varian')->get();

            foreach ($variants as $variant) {
                DB::table('menu')
                    ->where('id', $variant->menu_id)
                    ->update(['price' => $variant->price]);
            }
        }

        // 3. Set variant_group and variant_label from menu names matching "(Label)" pattern
        $menus = DB::table('menu')->whereNull('deleted_at')->orWhereNotNull('deleted_at')->get();
        foreach ($menus as $menu) {
            if (preg_match('/^(.+?)\s*\(([^)]+)\)\s*$/', $menu->name, $m)) {
                $baseName = trim($m[1]);
                $label = trim($m[2]);
                // Only treat as variant if label is not a combo like "Hot / Ice"
                if (! str_contains($label, '/')) {
                    DB::table('menu')
                        ->where('id', $menu->id)
                        ->update([
                            'variant_group' => $baseName,
                            'variant_label' => $label,
                        ]);
                }
            }
        }

        // 3b. Restore any previously merged menus and clean up dummy parent menus
        if (Schema::hasTable('menu')) {
            if (Schema::hasColumn('menu', 'merged_into_menu_id')) {
                DB::table('menu')->whereNotNull('merged_into_menu_id')->update([
                    'deleted_at' => null,
                    'merged_into_menu_id' => null,
                ]);
            }
            DB::table('menu')->where('name', 'like', '%(Hot / Ice)%')->delete();
        }

        // 4. Remap pesanan_item.menu_id and drop variant_id from pesanan_item
        if (Schema::hasTable('pesanan_item') && Schema::hasColumn('pesanan_item', 'variant_id')) {
            if (Schema::hasTable('menu') && Schema::hasColumn('menu', 'variant_id')) {
                $mappings = DB::table('menu')->whereNotNull('variant_id')->pluck('id', 'variant_id');
                foreach ($mappings as $vId => $mId) {
                    DB::table('pesanan_item')->where('variant_id', $vId)->update(['menu_id' => $mId]);
                }
            }

            Schema::table('pesanan_item', function (Blueprint $table) {
                // Drop FK first (MySQL)
                if (DB::getDriverName() === 'mysql') {
                    try {
                        $table->dropForeign('pesanan_item_variant_id_foreign');
                    } catch (Throwable) {
                    }
                }
                $table->dropColumn('variant_id');
            });
        }

        // 5. Drop variant_id FK and column from menu
        if (Schema::hasTable('menu') && Schema::hasColumn('menu', 'variant_id')) {
            Schema::table('menu', function (Blueprint $table) {
                if (DB::getDriverName() === 'mysql') {
                    try {
                        $table->dropForeign('menu_variant_id_foreign');
                    } catch (Throwable) {
                    }
                }
                $table->dropColumn('variant_id');
            });
        }

        // 6. Drop menu_varian table
        Schema::dropIfExists('menu_varian');

        // 7. Remove migration entries for old variant migrations
        //    so they won't be re-run if someone migrates fresh from this point
        $variantMigrations = [
            '2026_10_12_070003_create_menu_variants_table',
        ];
        DB::table('migrations')->whereIn('migration', $variantMigrations)->delete();
    }

    public function down(): void
    {
        // Re-create menu_varian
        Schema::create('menu_varian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('menu_id')->constrained('menu')->cascadeOnDelete();
            $table->string('name');
            $table->string('size')->nullable();
            $table->decimal('price', 12, 2)->default(0);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_default')->default(false);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });

        // Re-add variant_id to menu
        if (! Schema::hasColumn('menu', 'variant_id')) {
            Schema::table('menu', function (Blueprint $table) {
                $table->foreignId('variant_id')->nullable()->constrained('menu_varian')->nullOnDelete();
            });
        }

        // Re-add variant_id to pesanan_item
        if (! Schema::hasColumn('pesanan_item', 'variant_id')) {
            Schema::table('pesanan_item', function (Blueprint $table) {
                $table->foreignId('variant_id')->nullable()->constrained('menu_varian')->nullOnDelete();
            });
        }

        // Drop grouping columns
        Schema::table('menu', function (Blueprint $table) {
            if (Schema::hasColumn('menu', 'variant_group')) {
                $table->dropColumn('variant_group');
            }
            if (Schema::hasColumn('menu', 'variant_label')) {
                $table->dropColumn('variant_label');
            }
        });
    }
};
