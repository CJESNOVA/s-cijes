<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['plateforme_id', 'nom', 'code', 'etat'];

    public function plateforme()
    {
        return $this->belongsTo(Plateforme::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
