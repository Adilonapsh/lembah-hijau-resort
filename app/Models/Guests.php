<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Guests extends Model
{
    protected $table = 'guests';
    protected $guarded = ["id"];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }
}
