<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $fillable = [
        'reference', 'titre', 'description', 'user_id', 'plateforme_id', 
        'module_id', 'categorie_id', 'priorite_id', 'statut_id', 
        'technicien_id', 'date_ouverture', 'date_fermeture'
    ];

    // Les appartenances
    public function demandeur() { return $this->belongsTo(User::class, 'user_id'); }
    public function user() { return $this->belongsTo(User::class, 'user_id'); } // Alias pour compatibilité
    public function technicien() { return $this->belongsTo(User::class, 'technicien_id'); }
    public function plateforme() { return $this->belongsTo(Plateforme::class); }
    public function module() { return $this->belongsTo(Module::class); }
    
    // Les classifications
    public function categorie() { return $this->belongsTo(TicketCategorie::class); }
    public function priorite() { return $this->belongsTo(TicketPriorite::class); }
    public function statut() { return $this->belongsTo(TicketStatut::class); }

    // Les historiques et messages
    public function messages() { return $this->hasMany(TicketMessage::class); }
    public function historiques() { return $this->hasMany(TicketHistorique::class); }
    public function satisfaction()
    {
        return $this->hasOne(TicketSatisfaction::class);
    }

    public function fichiers()
    {
        return $this->hasMany(TicketFichier::class);
    }
}
