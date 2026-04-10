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
        Schema::table('room_categories', function (Blueprint $table) {
            $table->unsignedInteger('room_size_sqm')->nullable()->after('max_capacity');
            $table->unsignedInteger('base_occupancy')->default(2)->after('room_size_sqm');
            $table->decimal('extra_person_charge', 10, 2)->default(0)->after('base_occupancy');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('room_categories', function (Blueprint $table) {
            $table->dropColumn(['room_size_sqm', 'base_occupancy', 'extra_person_charge']);
        });
    }
};
