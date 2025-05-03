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
        Schema::create('sign_chassis_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sign_id')->unique()->constrained('signs')->onDelete('cascade');
            $table->integer('count');
            $table->string('columns_description');
            $table->text('location_from_road');
            $table->string('base');
            $table->float('disatance_from_road_edge');
            $table->float('column_radius');
            $table->float('column_height');
            $table->string('column_color');
            $table->string('column_type');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sign_chassis_details');
    }
};
