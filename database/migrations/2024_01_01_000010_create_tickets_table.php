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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('reference')->unique(); // TCK-2026-XXXX
            $table->string('titre');
            $table->text('description');
            
            // Relations
            $table->foreignId('user_id')->constrained('users'); // Demandeur
            $table->foreignId('plateforme_id')->constrained('plateformes');
            $table->foreignId('module_id')->constrained('modules');
            $table->foreignId('categorie_id')->constrained('ticket_categories');
            $table->foreignId('priorite_id')->constrained('ticket_priorites');
            $table->foreignId('statut_id')->constrained('ticket_statuts');
            $table->foreignId('technicien_id')->nullable()->constrained('users');
            
            $table->timestamp('date_ouverture')->useCurrent();
            $table->timestamp('date_fermeture')->nullable();
            $table->integer('delai_reponse_minutes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
