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
        Schema::create('knowledge_base_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('article_id')->constrained('knowledge_bases')->onDelete('cascade');
            $table->string('nom_fichier', 255);
            $table->string('nom_original', 255);
            $table->string('chemin', 255);
            $table->string('mime_type', 100);
            $table->integer('taille');
            $table->string('description', 500)->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();
            
            $table->index(['article_id', 'is_primary']);
            $table->index(['mime_type']);
            $table->index(['created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('knowledge_base_attachments');
    }
};
