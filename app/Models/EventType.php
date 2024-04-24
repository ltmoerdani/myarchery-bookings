<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventType extends Model
{
  use HasFactory;

  protected $table = 'event_type';
  protected $fillable = [
    'event_type',
    'event_id',
    'shared_type',
    'link_event',
    'code',
    'description',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
