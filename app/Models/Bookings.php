<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bookings extends Model
{
    use HasFactory;
    protected $table = 'bookings';

    protected $guarded = [];

    public function room()
    {
        return $this->belongsTo(Rooms::class, 'room_id', 'id');
    }
}
