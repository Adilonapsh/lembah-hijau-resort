<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RoomReportHistory extends Model
{
    protected $table = 'room_report_history';
    protected $guarded = ["id"];

    protected $casts = [
        'data_history' => 'json',
    ];

    public function room()
    {
        return $this->hasMany(Room::class, 'id', 'room_id');
    }
}
