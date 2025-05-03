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
        Schema::create('sign_locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sign_id')->unique()->constrained('signs')->onDelete('cascade');
            $table->decimal('latitude', 10, 8)->unique();
            $table->decimal('longitude', 11, 8)->unique();
            $table->string('governorate');
            $table->string('willayat');
            $table->string('village');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign_locations');
    }
};
