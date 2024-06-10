<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use HasFactory;
    protected $table = 'participant';
    protected $fillable = [
        'fname',
        'lname',
        'gender',
        'birthdate',
        'county_id',
        'country',
        'city_id',
        'city',
        'category',
        'delegation_id',
        'customer_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
