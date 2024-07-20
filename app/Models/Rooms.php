<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rooms extends Model
{
    use HasFactory;
    protected $table = 'rooms';
   
    protected $guarded = [];

    public function bookings()
    {
        return $this->hasMany(Bookings::class, 'room_id', 'id');
    }
}
