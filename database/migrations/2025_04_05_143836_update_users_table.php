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
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('password');
            $table->timestamp('phone_verified_at')->nullable()->after('phone');
            $table->enum('type', ['Admin', 'User', 'Mobile'])->default('User')->after('phone_verified_at');
            $table->string('mobile_id')->nullable()->unique()->after('password');
            $table->softDeletes()->after('updated_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('phone');
            $table->dropColumn('phone_verified_at');
            $table->dropColumn('type');
            $table->dropColumn('mobile_id');
            $table->dropSoftDeletes();
        });
    }
};
