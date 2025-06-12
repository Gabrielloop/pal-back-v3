<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->string('isbn');
            $table->foreign('isbn')->references('isbn')->on('books')->onDelete('cascade');

            $table->timestamps();

            $table->primary(['user_id', 'isbn']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};
