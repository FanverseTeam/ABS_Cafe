<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cafe_tables', function (Blueprint $table) {
            $table->id();
            $table->string('number', 20)->unique();
            $table->unsignedInteger('capacity')->default(2);
            $table->enum('area', ['indoor', 'outdoor'])->default('indoor');
            $table->enum('status', ['kosong', 'terisi', 'dipesan'])->default('kosong');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cafe_tables');
    }
};
