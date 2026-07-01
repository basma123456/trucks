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
            $table->string('part_description', 1000)->nullable();
            $table->decimal('price', 10, 2)->nullable()->default(0);
            $table->string('thumbnail')->nullable();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropColumns('items', ['part_description', 'price' , 'thumbnail']);
    }
};
