<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddImageLogAndImageLarToDetailedSignsTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detailed_signs', function (Blueprint $table) {
            $table->string('image_log')->nullable()->after('comments');
            $table->string('image_lar')->nullable()->after('image_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detailed_signs', function (Blueprint $table) {
            $table->dropColumn(['image_log', 'image_lar']);
        });
    }
}
