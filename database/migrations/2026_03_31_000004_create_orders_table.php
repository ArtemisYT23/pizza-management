<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('pizza_id')->constrained()->cascadeOnDelete();
            $table->timestamp('ordered_at');
            $table->timestamps();

            $table->index(['user_id', 'ordered_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
