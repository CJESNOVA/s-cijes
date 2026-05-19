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
        Schema::create('plateformes', function (Blueprint $table) {
            $table->id();
            $table->string('nom');
            $table->string('code')->unique(); // ex: CIJET, CEPROSAT
            $table->string('url')->nullable();
            $table->string('cle_api')->unique();
            $table->string('secret_key');
            $table->boolean('etat')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plateformes');
    }
};
