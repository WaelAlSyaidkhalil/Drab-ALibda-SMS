<?php

namespace App\Console\Commands;

use App\Jobs\FillAttendanceTableJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('school:fill-attendance-table')]
#[Description('this command fills attendance table')]
class FillAttendanceTable extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        FillAttendanceTableJob::dispatch();

        $this->info('Filling attendance table job has been dispatched successfully.');

        return self::SUCCESS;
    }
}
