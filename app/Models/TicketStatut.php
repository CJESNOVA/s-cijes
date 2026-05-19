<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketStatut extends Model 
{ 
    protected $fillable = ['nom', 'code']; 

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'statut_id');
    }
}
