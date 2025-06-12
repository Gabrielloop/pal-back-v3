<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('comments', function (Blueprint $table) {
            $table->string('isbn');
            $table->foreign('isbn')->references('isbn')->on('books')->onDelete('cascade');

            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            $table->text('comment_content');

            $table->timestamps();

            $table->primary(['isbn', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
