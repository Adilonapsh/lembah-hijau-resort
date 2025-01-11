<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';
    protected $guarded = ["id"];

    public function guests()
    {
        return $this->hasMany(Guests::class, 'id_kamar', 'id');
    }
}
