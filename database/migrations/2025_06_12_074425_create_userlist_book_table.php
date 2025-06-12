<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('userlist_book', function (Blueprint $table) {
            $table->unsignedBigInteger('userlist_id');
            
            $table->foreign('userlist_id')->references('userlist_id')->on('userlists')->onDelete('cascade');

            $table->string('isbn');
            
            $table->primary(['userlist_id', 'isbn']);

            $table->timestamps();

            $table->foreign('isbn')->references('isbn')->on('books')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('userlist_book');
    }
};
