<?php

namespace App\Enums;

enum MatchStatus: string
{
    case NotStarted = 'not_started';
    case Live = 'live';
    case Finished = 'finished';

    public function label(): string
    {
        return match ($this) {
            self::NotStarted => __('backend.not_started_yet'),
            self::Live => __('backend.live_now'),
            self::Finished => __('backend.finished'),
        };
    }
}
