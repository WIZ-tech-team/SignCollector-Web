<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddGpsAccuracyAndImageLocationToDetailedSignsTable extends Migration
{
    public function up()
    {
        Schema::table('detailed_signs', function (Blueprint $table) {
            // numeric field for GPS accuracy (nullable)
            $table->decimal('gps_accuracy', 8, 2)
                  ->nullable()
                  ->after('longitude');

            // optional string to store image location/path
            $table->string('image_location', 255)
                  ->nullable()
                  ->after('image_lar');
        });
    }

    public function down()
    {
        Schema::table('detailed_signs', function (Blueprint $table) {
            $table->dropColumn(['gps_accuracy', 'image_location']);
        });
    }
}
