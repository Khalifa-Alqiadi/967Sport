<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Venue extends Model
{
    public $incrementing = false;
    protected $keyType = 'int';
    protected $guarded = [];

    public function getImagePathAttribute($value)
    {
        if (!empty($this->attributes['local_image'])) {
            return route('fileView', ['path' => 'venues/' . $this->attributes['local_image']]);
        }
        return $value;
    }

    public function getApiImagePathAttribute()
    {
        return $this->attributes['image_path'] ?? null;
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function teams()
    {
        return $this->hasMany(Team::class, 'venue_id');
    }
}
