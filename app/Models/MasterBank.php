<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MasterBank extends Model
{
    use HasFactory;
    protected $table = 'master_bank';
    protected $fillable = [
        'id',
        'type',
        'bank_code',
        'bank_name',
        'payment_method',
        'endpoint',
        'is_active',
        'description',
    ];

    protected $casts = [
        'deleted_at' => 'datetime:Y-m-d H:m:s',
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];
}
