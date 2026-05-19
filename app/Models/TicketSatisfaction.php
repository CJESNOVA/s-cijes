<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TicketSatisfaction extends Model
{
    protected $fillable = ['ticket_id', 'note', 'commentaire'];

    public function ticket() { return $this->belongsTo(Ticket::class); }
}
