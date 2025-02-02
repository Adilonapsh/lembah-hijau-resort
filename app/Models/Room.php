<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    protected $table = 'rooms';

    protected $guarded = ['id'];

    protected $casts = [
        'guests_count' => 'integer',
        'kapasitas' => 'integer',
    ];

    public function guests()
    {
        return $this->hasMany(Guests::class, 'id_kamar', 'id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }
}
