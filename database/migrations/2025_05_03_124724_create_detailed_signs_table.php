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
        Schema::create('detailed_signs', function (Blueprint $table) {
            $table->id();
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
            // Road
            $table->string('road_classification')->nullable();
            $table->string('road_name')->nullable();
            $table->string('road_number')->nullable();
            $table->string('road_type')->nullable();
            $table->text('road_direction')->nullable();
            // Location
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->string('governorate')->nullable();
            $table->string('willayat')->nullable();
            $table->string('village')->nullable();
            // Chassis
            $table->integer('signs_count')->nullable();
            $table->string('columns_description')->nullable();
            $table->text('sign_location_from_road')->nullable();
            $table->string('sign_base')->nullable();
            $table->float('distance_from_road_edge_meter')->nullable();
            $table->float('sign_column_radius_mm')->nullable();
            $table->float('column_height')->nullable();
            $table->string('column_colour')->nullable();
            $table->string('column_type')->nullable();
            // Content
            $table->string('sign_content_shape_description')->nullable();
            $table->text('sign_content_arabic_text')->nullable();
            $table->text('sign_content_english_text')->nullable();
            // Other
            $table->string('sign_condition')->nullable();
            $table->text('comments')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detailed_signs');
    }
};
