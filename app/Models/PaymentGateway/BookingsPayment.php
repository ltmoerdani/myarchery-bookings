<?php

namespace App\Models\PaymentGateway;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BookingsPayment extends Model
{
    use HasFactory;
    protected $table = 'bookings_payment';
    protected $fillable = [
        'booking_id',
        'payment_type',
        'callback',
        'external_id',
        'payment_method',
        'status',
        'amount',
        'paid_amount',
        'bank_code',
        'paid_at',
        'payer_email',
        'description',
        'adjusted_received_amount',
        'fees_paid_amount',
        'payment_destination',
        'paid_at',
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:m:s',
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
