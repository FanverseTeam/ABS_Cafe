<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('menus', function (Blueprint $table) {
            $table->foreignId('merged_into_menu_id')
                ->nullable()
                ->after('id')
                ->constrained('menus')
                ->nullOnDelete();
            $table->foreignId('variant_id')
                ->nullable()
                ->after('merged_into_menu_id')
                ->constrained('menu_variants')
                ->nullOnDelete();
            $table->softDeletes();
        });

        Schema::table('menu_variants', function (Blueprint $table) {
            $table->string('size')->nullable()->after('name');
            $table->boolean('is_available')->default(true)->after('price');
            $table->unsignedInteger('sort_order')->default(0)->after('is_default');
        });

        Schema::table('promos', function (Blueprint $table) {
            $table->foreignId('target_menu_id')
                ->nullable()
                ->after('target_category_id')
                ->constrained('menus')
                ->nullOnDelete();
        });

        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('menu_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['user_id', 'menu_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');

        Schema::table('promos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('target_menu_id');
        });

        Schema::table('menu_variants', function (Blueprint $table) {
            $table->dropColumn(['size', 'is_available', 'sort_order']);
        });

        Schema::table('menus', function (Blueprint $table) {
            $table->dropConstrainedForeignId('variant_id');
            $table->dropConstrainedForeignId('merged_into_menu_id');
            $table->dropSoftDeletes();
        });
    }
};
