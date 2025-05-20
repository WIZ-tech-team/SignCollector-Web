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
        Schema::create('signs_groups', function (Blueprint $table) {
            $table->id();
            // Location
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('gps_accuracy', 8, 2)->nullable();
            $table->string('governorate')->nullable();
            $table->string('willayat')->nullable();
            $table->string('village')->nullable();
            // Road
            $table->string('road_classification')->nullable();
            $table->string('road_name')->nullable();
            $table->string('road_number')->nullable();
            $table->string('road_type')->nullable();
            $table->text('road_direction')->nullable();
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
            // Other
            $table->text('comments')->nullable();
            $table->string('image_log')->nullable();
            $table->string('image_lar')->nullable();
            $table->string('image_location', 255)->nullable();
            $table->string('created_by')->nullable();
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
