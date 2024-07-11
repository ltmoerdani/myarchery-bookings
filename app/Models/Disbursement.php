<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Disbursement extends Model
{
    use HasFactory;
    protected $table = 'disbursements';
    protected $fillable = [
        'withdraw_id',
        'payment_type',
        'callback',
        'req_header',
        'callback_id',
        'external_id',
        'amount',
        'bank_code',
        'account_holder_name',
        'disbursement_description',
        'status',
        'currency',
        'description',
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:m:s',
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
