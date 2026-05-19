<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketPriorite extends Model 
{ 
    protected $fillable = ['nom', 'niveau', 'temps_max_heures']; 

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'priorite_id');
    }
}
