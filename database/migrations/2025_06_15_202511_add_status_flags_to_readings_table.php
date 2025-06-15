<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
{
    Schema::table('readings', function (Blueprint $table) {
        $table->boolean('is_started')->default(false);
        $table->boolean('is_finished')->default(false);
        $table->boolean('is_abandoned')->default(false);
        $table->boolean('is_reading')->default(false)->after('is_abandoned');
    });
}

public function down(): void
{
    Schema::table('readings', function (Blueprint $table) {
        $table->dropColumn(['is_started', 'is_finished', 'is_abandoned', 'is_reading']);
    });
}
};
