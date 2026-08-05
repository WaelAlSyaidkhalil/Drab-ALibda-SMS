<?php

namespace App\Console\Commands;

use App\Jobs\FillTeacherAttendanceTableJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('school:fill-teacher-attendance')]
#[Description('this command fills teacher attendance table')]
class FillTeacherAttendanceTable extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        FillTeacherAttendanceTableJob::dispatch();

        $this->info('Filling teacher attendance table job has been dispatched successfully.');

        return self::SUCCESS;
    }
}
