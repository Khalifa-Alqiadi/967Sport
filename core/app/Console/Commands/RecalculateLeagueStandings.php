<?php

namespace App\Console\Commands;

use App\Services\Football\LeagueStandingsCalculator;
use Illuminate\Console\Command;

class RecalculateLeagueStandings extends Command
{
    protected $signature = 'football:standings
        {--league= : Recalculate one league ID}
        {--season= : Recalculate one season ID}';

    protected $description = 'Recalculate football league standings from finished fixtures';

    public function handle(LeagueStandingsCalculator $calculator): int
    {
        $summary = $calculator->recalculate(
            $this->option('league') ? (int) $this->option('league') : null,
            $this->option('season') ? (int) $this->option('season') : null,
        );

        $this->info(sprintf(
            'Standings updated: %d competitions, %d tables, %d teams, %d counted matches.',
            $summary['competitions'],
            $summary['tables'],
            $summary['teams'],
            $summary['matches'],
        ));

        return self::SUCCESS;
    }
}
