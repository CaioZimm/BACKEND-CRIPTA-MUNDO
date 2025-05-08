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
        Schema::create('chapters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_id');  
            $table->timestamps();
            $table->string('title', 50);
            $table->longText('content');
            $table->string('image')->nullable()->comment('Foto de background');

            // Relacionamento VÁRIOS capitulos para 1 POST;
            $table->foreign('post_id')->references('id')->on('posts')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chapters');
    }
};
