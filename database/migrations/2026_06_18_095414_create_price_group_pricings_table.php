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
        Schema::create('price_group_pricings', function (Blueprint $table) {
            $table->id();
            $table->string('group_id')->nullable();
            $table->string('group_name')->nullable();
            $table->string('item_code')->nullable();
            $table->string('brand_code')->nullable();
            $table->string('price')->nullable();
            $table->string('price_name')->nullable();
            $table->unique(['price_name', 'item_code']);
            $table->boolean('can_purchase')->default(0)->nullable();
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('price_group_pricings');
    }
};
