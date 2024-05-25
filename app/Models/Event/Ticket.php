<?php

namespace App\Models\Event;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Event\TicketVariation;

class Ticket extends Model
{
  use HasFactory;

  protected $fillable = [
    'event_id',
    'competition_id',
    'event_type',
    'title',
    'ticket_available_type',
    'ticket_available',
    'max_ticket_buy_type',
    'max_buy_ticket',
    'description',
    'pricing_scheme',
    'pricing_type',
    'price',
    'f_price',
    'international_price',
    'early_bird_discount_type',
    'early_bird_discount',
    'early_bird_discount_amount',
    'early_bird_discount_amount_international',
    'early_bird_discount_date',
    'early_bird_discount_time',
    'late_price_discount',
    'late_price_discount_amount',
    'late_price_discount_amount_international',
    'late_price_discount_date',
    'late_price_discount_time',
    'late_price_discount_type',
    'variations',
    'trans_vars'
  ];
  //ticket_variations
  public function ticket_variations()
  {
    return $this->hasMany(TicketVariation::class);
  }
}
