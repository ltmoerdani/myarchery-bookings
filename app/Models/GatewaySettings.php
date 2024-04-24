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
    'currency_id',
    'gateway_type',
    'gateway_id',
    'percentage_status',
    'charge',
    'fee',
    'min_limit',
    'max_limit',
    'is_active',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
