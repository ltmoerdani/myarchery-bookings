<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketPrice extends Model
{
  use HasFactory;

  protected $table = 'ticket_price';
  protected $fillable = [
    'ticket_id',
    'currency_id',
    'title',
    'ticket_available_type',
    'ticket_available',
    'max_ticket_buy_type',
    'max_buy_ticket',
    'pricing_type',
    'price',
    'f_price',
    'early_bird_discount',
    'early_bird_discount_amount',
    'early_bird_discount_type',
    'early_bird_discount_date',
    'early_bird_discount_time',
    'variations',
    'description',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];
}
