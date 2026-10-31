<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\DailyReportController;

class AutoStopTasksCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tasks:auto-stop';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Automatically stop in-progress tasks that exceeded 2 hours or reached 1:00 PM / 6:30 PM cutoff';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $stopped = DailyReportController::checkAndAutoStopTasks();
        $this->info("Auto-stop check completed. Stopped tasks: {$stopped}");
        return Command::SUCCESS;
    }
}

