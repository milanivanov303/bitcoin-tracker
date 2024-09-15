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
        Schema::create('percent_alerts', function (Blueprint $table) {
            $table->id();
            $table->string('email')->unique();
            $table->decimal('percent', 6, 2);
            $table->enum('interval', [1, 6, 24]);
            $table->foreignId('user_id')->constrained('users');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('percent_alerts');
    }
};
