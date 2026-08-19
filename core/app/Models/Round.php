<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Round extends Model
{
    protected $fillable = [
        'id',
        'league_id',
        'season_id',
        'stage_id',
        'name',
        'finished',
        'is_current',
        'games_in_current_week',
        'starting_at',
        'ending_at',
    ];

    protected $casts = [
        'finished' => 'boolean',
        'is_current' => 'boolean',
        'games_in_current_week' => 'boolean',
        'starting_at' => 'datetime',
        'ending_at' => 'datetime',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class, 'league_id');
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function stage(): BelongsTo
    {
        return $this->belongsTo(Stage::class, 'stage_id');
    }

    public function fixtures(): HasMany
    {
        return $this->hasMany(Fixture::class, 'round_id');
    }
}
