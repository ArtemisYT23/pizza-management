<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingredient_pizza', function (Blueprint $table) {
            $table->foreignUuid('ingredient_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('pizza_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->primary(['ingredient_id', 'pizza_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingredient_pizza');
    }
};
