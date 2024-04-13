<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndonesianDistrict extends Model
{
    use HasFactory;

    protected $table = 'indonesian_district';
    protected $fillable = [
        'id',
        'id_area',
        'kode',
        'province_id',
        'city_id',
        'subdistrict_id',
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

    public function indonesianCities()
    {
        return $this->belongsTo(IndonesianCities::class);
    }

    public function indonesianSubdistrict()
    {
        return $this->belongsTo(IndonesianSubdistrict::class);
    }
}
