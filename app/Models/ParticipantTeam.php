<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantTeam extends Model
{
  use HasFactory;

  protected $fillable = [
    'name',
    'participant_id',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];

  public function participant()
  {
    return $this->belongsTo(Participant::class);
  }
}
