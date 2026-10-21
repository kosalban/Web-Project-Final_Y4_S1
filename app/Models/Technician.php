<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Technician extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'email', 'phone', 'specialization', 'status',
    ];

    public function tickets()
    {
        return $this->hasMany(Ticket::class);
    }
}
