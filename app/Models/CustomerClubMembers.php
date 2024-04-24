<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerClubMembers extends Model
{
  use HasFactory;

  protected $table = 'customer_club_members';
  protected $fillable = [
    'customer_id',
    'club_id',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
