<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Plateforme extends Model
{
    protected $fillable = ['nom', 'code', 'url', 'cle_api', 'secret_key', 'etat'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function modules()
    {
        return $this->hasMany(Module::class);
    }

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
