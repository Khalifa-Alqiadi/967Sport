<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $fillable = [
        'id',
        'league_id',
        'season_id',
        'round_id',
        'stage_id',
        'group_id',
        'venue_id',
        'home_team_id',
        'away_team_id',
        'starting_at',
        'state_id',
        'stage_id',
        'state_name',
        'state_code',
        'home_score',
        'away_score',
        'ht_home_score',
        'ht_away_score',
        'is_finished',
        'counts_for_standings',
        'ft_home_score',
        'ft_away_score',
        'standing_home_score',
        'standing_away_score',
        'standing_home_points_adjustment',
        'standing_away_points_adjustment',
        'standing_adjustment_notes',
        'et_home_score',
        'et_away_score',
        'pen_home',
        'pen_away',
        'minute',
        'first_half_added_time',
        'second_half_added_time',
        'payload',
        'is_slider',
        'is_home',
        'lineups_json',
        'details_synced_at',
        'injuries_json',
        'suspensions_json',
        'venue_json',
    ];

    protected $casts = [
        'lineups_json'           => 'array',
        'details_synced_at'      => 'datetime',
        'starting_at'            => 'datetime',
        'payload' => 'array',
        'injuries_json' => 'array',
        'suspensions_json' => 'array',
        'venue_json' => 'array',
        'is_finished' => 'boolean',
        'counts_for_standings' => 'boolean',
    ];

    public function league()
    {
        return $this->belongsTo(League::class, 'league_id');
    }

    public function season()
    {
        return $this->belongsTo(Season::class, 'season_id');
    }

    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }

    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }

    public function round()
    {
        return $this->belongsTo(Round::class, 'round_id', 'id');
    }

    public function stage()
    {
        return $this->belongsTo(Stage::class, 'stage_id', 'id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id', 'id');
    }

    public function events()
    {
        return $this->hasMany(FixtureEvent::class)->orderBy('sort_order')->orderBy('id');
    }

}
