<?php

namespace App\Console\Commands;

use App\Jobs\CreateStudentEnrollmentsJob;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Queue\Jobs\Job;

#[Signature('school:generate-enrollments')]
#[Description('this command generates student enrollments')]
class CreateStudentEnrollments extends Command
{
    /**
     * Execute the console command.
     */
    public function handle()
    {
        CreateStudentEnrollmentsJob::dispatch();

        $this->info('Enrollment generation job has been dispatched successfully.');

        return self::SUCCESS;
    }
}
