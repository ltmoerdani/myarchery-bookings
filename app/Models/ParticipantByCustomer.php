<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParticipantByCustomer extends Model
{
  use HasFactory;
  protected $table = 'participant_by_customer';

  protected $fillable = [
    'customer_id',
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
