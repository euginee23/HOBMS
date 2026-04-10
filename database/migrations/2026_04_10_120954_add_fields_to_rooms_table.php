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
        Schema::table('rooms', function (Blueprint $table) {
            $table->string('bed_type')->nullable()->after('floor');
            $table->unsignedTinyInteger('bed_count')->default(1)->after('bed_type');
            $table->string('view_type')->default('none')->after('bed_count');
            $table->boolean('is_smoking')->default(false)->after('view_type');
            $table->timestamp('last_cleaned_at')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->dropColumn(['bed_type', 'bed_count', 'view_type', 'is_smoking', 'last_cleaned_at']);
        });
    }
};
