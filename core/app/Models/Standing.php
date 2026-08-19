<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Standing extends Model
{
    public $incrementing = false;
    protected $fillable = [
        'id',
        'league_id',
        'season_id',
        'stage_id',
        'round_id',
        'group_id',
        'team_id',
        'sportmonks_standing_id',
        'participant_id',
        'group_name',
        'standing_type',
        'position',
        'points',
        'played',
        'won',
        'draw',
        'lost',
        'goals_for',
        'goals_against',
        'goal_difference',
        'recent_form_points',
        'form',
        'payload_json',
        'rule_id',
        'rule_name',
        'rule_type_id',
        'rule_type_code',
        'rule_type_name',
        'synced_at',
    ];

    protected $casts = [
        'payload_json' => 'array',
        'synced_at'    => 'datetime',
    ];

    public function participant(): BelongsTo
    {
        return $this->belongsTo(Team::class, 'participant_id', 'id');
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(Group::class, 'group_id');
    }
}
