<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('posts', function (Blueprint $table) {
            $table->id('post_id'); 
            $table->string('title');
            $table->text('content');
            $table->string('image_path')->nullable();
            $table->integer('upvote_count')->default(0);
            $table->foreignId('users_id')->nullable(); 
            $table->string('tags')->nullable();             
            $table->timestamps();
        });
    }

    public function down(): void {
        Schema::dropIfExists('posts');
    }
};