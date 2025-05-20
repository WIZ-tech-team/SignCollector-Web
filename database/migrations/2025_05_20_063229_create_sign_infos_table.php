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
        Schema::create('sign_infos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('signs_group_id')->constrained('signs_groups', 'id')->onDelete('cascade');
            // Sign
            $table->string('sign_name')->nullable();
            $table->string('sign_code')->nullable();
            $table->string('sign_code_gcc')->nullable();
            $table->string('sign_type')->nullable();
            $table->string('sign_shape')->nullable();
            $table->float('sign_length')->nullable();
            $table->float('sign_width')->nullable();
            $table->float('sign_radius')->nullable();
            $table->string('sign_color')->nullable();
            // Content
            $table->string('sign_content_shape_description')->nullable();
            $table->text('sign_content_arabic_text')->nullable();
            $table->text('sign_content_english_text')->nullable();
            // Other
            $table->string('sign_condition')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign_infos');
    }
};
