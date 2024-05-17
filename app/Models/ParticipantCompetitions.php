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
        'participant_id',
        'ticket_id',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
