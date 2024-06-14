<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantCompetitions extends Model
{
    use HasFactory;
    protected $table = 'participant_competitions';
    protected $fillable = [
        'competition_name',
        'event_id',
        'participant_id',
        'ticket_id',
        'booking_id',
        'description',
        'category',
        'delegation_id',
        'customer_id',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
