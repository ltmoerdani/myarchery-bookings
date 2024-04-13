<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndonesianCities extends Model
{
    use HasFactory;

    protected $table = 'indonesian_cities';
    protected $fillable = [
        'id',
        'id_area',
        'kode',
        'province_id',
        'name',
        'level',
        'timezones',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'created_at' => 'datetime:Y-m-d H:m:s',
        'updated_at' => 'datetime:Y-m-d H:m:s'
    ];

    public function indonesianProvince()
    {
        return $this->belongsTo(IndonesianProvince::class);
    }

    public function indonesianSubdistrict()
    {
        return $this->hashMany(IndonesianSubdistrict::class);
    }

    public function indonesianDistrict()
    {
        return $this->hashMany(IndonesianDistrict::class);
    }
}
