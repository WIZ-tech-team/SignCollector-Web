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
        Schema::create('sign_contents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sign_id')->unique()->constrained('signs')->onDelete('cascade');
            $table->string('shape_description');
            $table->json('written_content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign_contents');
    }
};
