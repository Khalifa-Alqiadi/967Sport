<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixtureEvent extends Model
{
    public const TYPE_GOAL = 'goal';
    public const TYPE_OWN_GOAL = 'own_goal';
    public const TYPE_PENALTY = 'penalty';
    public const TYPE_YELLOW_CARD = 'yellow_card';
    public const TYPE_RED_CARD = 'red_card';
    public const TYPE_SUBSTITUTION = 'substitution';

    protected $guarded = [];

    protected $casts = [
        'is_own_goal' => 'boolean',
        'is_penalty' => 'boolean',
        'rescinded' => 'boolean',
        'payload' => 'array',
    ];

    public function fixture()
    {
        return $this->belongsTo(Fixture::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function player()
    {
        return $this->belongsTo(Player::class);
    }

    public function assistPlayer()
    {
        return $this->belongsTo(Player::class, 'assist_player_id');
    }

    public function playerIn()
    {
        return $this->belongsTo(Player::class, 'player_in_id');
    }

    public function playerOut()
    {
        return $this->belongsTo(Player::class, 'player_out_id');
    }

    public function scopeGoals($query)
    {
        return $query->whereIn('type', [self::TYPE_GOAL, self::TYPE_OWN_GOAL, self::TYPE_PENALTY]);
    }
}
