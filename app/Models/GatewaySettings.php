<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GatewaySettings extends Model
{
  use HasFactory;

  protected $table = 'gateway_settings';
  protected $fillable = [
    'payment_method',
    'gateway_type',
    'currency',
    'payment_channel',
    'percentage_amount',
    'fixed_amount',
    'fee',
    'min_limit',
    'max_limit',
    'is_active',
  ];

  protected $casts = [
    'deleted_at' => 'datetime:Y-m-d H:m:s',
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
