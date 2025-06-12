<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('userlists', function (Blueprint $table) {
            $table->id('userlist_id');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('userlist_name');
            $table->string('userlist_description')->nullable();
            $table->string('userlist_type');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userlists');
    }
};
