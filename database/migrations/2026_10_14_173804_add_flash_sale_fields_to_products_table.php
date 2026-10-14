<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->decimal('flash_sale_price', 12, 2)->nullable();
        $table->timestamp('flash_sale_starts_at')->nullable();
        $table->timestamp('flash_sale_ends_at')->nullable();
    });
}

public function down(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->dropColumn([
            'flash_sale_price',
            'flash_sale_starts_at',
            'flash_sale_ends_at',
        ]);
    });
}

};
