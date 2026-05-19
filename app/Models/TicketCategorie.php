<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketCategorie extends Model 
{ 
    protected $fillable = ['nom']; 

    public function tickets()
    {
        return $this->hasMany(Ticket::class, 'categorie_id');
    }
}
