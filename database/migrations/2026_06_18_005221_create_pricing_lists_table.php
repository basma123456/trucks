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
        Schema::create('pricing_lists', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('item_id')->nullable();
//            $table->foreignId('item_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('item_code')->nullable();
            $table->string('brand_code')->nullable();

            $table->decimal('price', 15, 2)->default(0);
            $table->string('price_name')->nullable();

            $table->boolean('can_purchase')->default(false);

            $table->decimal('purchase_cost', 15, 2)->nullable();

            $table->boolean('has_map')->default(false);

            $table->timestamps();

            // Uncomment if item_id references an items table
            // $table->foreign('item_id')
            //     ->references('id')
            //     ->on('items')
            //     ->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pricing_lists');
    }};
