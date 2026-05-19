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
        Schema::create('app_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // ticket_created, ticket_assigned, message_added, status_changed, etc.
            $table->string('title');
            $table->text('message');
            $table->string('notifiable_type')->nullable(); // Pour lier à ticket, user, etc.
            $table->unsignedBigInteger('notifiable_id')->nullable(); // Pour lier à ticket, user, etc.
            $table->json('data')->nullable(); // Données supplémentaires en JSON
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('app_notifications');
    }
};
