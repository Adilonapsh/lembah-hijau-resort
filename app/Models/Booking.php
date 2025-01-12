<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $guarded = ['id'];

    protected $casts = [
        'id_kamar' => 'json',
    ];

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas', 'id');
    }

}
