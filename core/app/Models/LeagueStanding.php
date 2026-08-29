<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeagueStanding extends Model
{
    protected $fillable = [
        'league_id',
        'season_id',
        'team_id',
        'group_id',
        'scope_key',
        'group_name',
        'position',
        'played',
        'won',
        'draw',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'base_points',
        'fixture_points_adjustment',
        'manual_points_adjustment',
        'points',
        'recent_form_points',
        'form',
        'calculated_at',
    ];

    protected $casts = [
        'calculated_at' => 'datetime',
    ];

    public function league(): BelongsTo
    {
        return $this->belongsTo(League::class);
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(Season::class);
    }

    public function team(): BelongsTo
    {
        return $this->belongsTo(Team::class);
    }

    /** Keep the same relation name used by the current standings Blade partial. */
    public function participant(): BelongsTo
    {
        return $this->team();
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class);
    }
}
