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
        Schema::table('tickets', function (Blueprint $table) {
            $table->foreignId('resolu_par')->nullable()->after('technicien_id')->constrained('users')->onDelete('set null');
            $table->timestamp('date_resolution')->nullable()->after('resolu_par');
            $table->foreignId('fusionne_avec')->nullable()->after('date_resolution')->constrained('tickets')->onDelete('set null');
            
            $table->index(['resolu_par', 'date_resolution']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tickets', function (Blueprint $table) {
            $table->dropIndex(['resolu_par', 'date_resolution']);
            $table->dropForeign(['fusionne_avec']);
            $table->dropColumn('fusionne_avec');
            $table->dropColumn('date_resolution');
            $table->dropForeign(['resolu_par']);
            $table->dropColumn('resolu_par');
        });
    }
};
