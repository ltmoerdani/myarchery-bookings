<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
  use HasFactory;
  protected $table = 'participant';
  protected $fillable = [
    'fname',
    'lname',
    'gender',
    'birthdate',
    'county_id',
    'country',
    'city_id',
    'city',
    'username',
  ];

  protected $casts = [
    'created_at' => 'datetime:Y-m-d H:m:s',
    'updated_at' => 'datetime:Y-m-d H:m:s'
  ];

  public function customers()
  {
    return $this->belongsToMany(Customer::class, 'participant_by_customer');
  }
}
