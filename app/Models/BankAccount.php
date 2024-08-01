<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankAccount extends Model
{
    use HasFactory;
    protected $table = 'bank_account';
    protected $fillable = [
        'role',
        'bank_id',
        'account_no',
        'account_name',
        'is_active',
        'description',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function bank()
    {
        return $this->belongsTo(MasterBank::class, 'bank_id', 'id');
    }

    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }
}
