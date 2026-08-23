<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $guarded = [];

    public function getImagePathAttribute($value)
    {
        if (!empty($this->attributes['local_image'])) {
            return route('fileView', ['path' => 'players/' . $this->attributes['local_image']]);
        }
        return $value;
    }

    public function getApiImagePathAttribute()
    {
        return $this->attributes['image_path'] ?? null;
    }

    public function teams()
    {
        return $this->belongsToMany(Team::class, 'team_players')
            ->withPivot([
                'id',
                'season_id',
                'position_id',
                'detailed_position_id',
                'jersey_number',
                'from_date',
                'to_date',
                'is_current',
                'is_captain',
                'transfer_id',
            ])
            ->withTimestamps();
    }

    public function country(){
        return $this->belongsTo(Country::class, 'country_id');
    }
}
