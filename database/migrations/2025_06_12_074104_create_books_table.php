<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('books', function (Blueprint $table) {
            $table->string('isbn')->primary();
            $table->string('book_title');
            $table->string('book_author');
            $table->string('book_publisher')->nullable();
            $table->year('book_year')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('books');
    }
};