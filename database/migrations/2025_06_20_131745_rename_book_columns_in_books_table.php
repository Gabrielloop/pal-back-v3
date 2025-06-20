<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('isbn', 'isbn');
            $table->renameColumn('book_title', 'title');
            $table->renameColumn('book_author', 'author');
            $table->renameColumn('book_publisher', 'publisher');
            $table->renameColumn('book_year', 'year');
        });
    }

    public function down(): void {
        Schema::table('books', function (Blueprint $table) {
            $table->renameColumn('isbn', 'isbn');
            $table->renameColumn('title', 'book_title');
            $table->renameColumn('author', 'book_author');
            $table->renameColumn('publisher', 'book_publisher');
            $table->renameColumn('year', 'book_year');
        });
    }
};