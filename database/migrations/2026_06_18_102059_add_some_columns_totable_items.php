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
        Schema::table('items', function (Blueprint $table) {
            $table->string('price_group_id')->nullable();
            $table->string('price_group')->nullable();
            $table->string('code')->after('id')->unique()->nullable();
            $table->string('brand_code')->nullable();

        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('items' , ['price_group_id' , 'price_group' , 'code' , 'brand_code']);
    }
};
