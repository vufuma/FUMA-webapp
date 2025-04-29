<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SubmitJob;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;
use Illuminate\Support\Facades\DB;
/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Closure based console
| commands. Each Closure is bound to a command instance allowing a
| simple approach to interacting with each command's IO methods.
|
*/

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/* Schedulilng deletion of faulty jobs
Logic: faulty jobs are removed if they are older than 1 month
*/

## Schedule a task to find the faulty jobs to be deleted
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.to_be_deleted.csv');
    $dir = config('app.jobdir');

    $jobs = Helper::findFaultyJobs();

    $results = [];
    foreach ($jobs as $job) {

        array_push($results, $job->toArray());
    }

    if (count($results) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $results);
})->weeklyOn(2, '10:45')
    ->environments('production')
    ->name('Find faulty jobs to be deleted')
    ->withoutOverlapping();


## Schedule a task to delete the faulty jobs
## Currently there is a delay so there are more jobs that are deleted than being listed as above because of the timestamp
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.schedule.csv');
    $dir = config('app.jobdir');

    $jobs = Helper::findFaultyJobs();

    $results = [];
    foreach ($jobs as $job) {

        if ($job->type == 'snp2gene' || $job->type == 'geneMap') {
            $job->dir = $dir . '/jobs/';
        } else {
            $job->dir = $dir . '/' . $job->type . '/';
        }

        $err = Helper::deleteJobByAdmin($job->dir, $job->jobID);

        if (!$err) {
            $err = 'Deleted';
        }
        $job->deletion_status = $err;

        array_push($results, $job->toArray());
    }

    if (count($results) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $results);
})->weeklyOn(2, '20:00')
    ->environments('production')
    ->name('Delete Faulty Jobs')
    ->withoutOverlapping();


## Schedule a task to list the OK jobs to be deleted
Schedule::call(function(){
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.ok_jobs_to_be_deleted.csv');
    $results = Helper::findOKJobs();

    if (count($results) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $results);
})->monthlyOn(4, '10:00') #run every month on the 4th at 10:00
    ->environments('production')
    ->name('Find OK jobs to be deleted')
    ->withoutOverlapping();

## Schedule a task to delete the OK jobs
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.ok_jobs_deleted.csv');
    $dir = config('app.jobdir');

    $jobs = Helper::findOKJobs();

    $result = [];
    foreach ($jobs as $job) {

        if ($job['type'] == 'snp2gene' || $job['type'] == 'geneMap') {
            $job['dir'] = $dir . '/jobs/';
        } else {
            $job['dir'] = $dir . '/' . $job['type'] . '/';
        }

        $err = Helper::deleteJobByAdmin($job['dir'], $job['jobID']);

        if (!$err) {
            $err = 'Deleted';
        }
        $job['deletion_status'] = $err;

        array_push($result, $job);
    }

    if (count($result) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $result);
})->monthlyOn(4, '16:00')
    ->environments('production')
    ->name('Delete OK Jobs')
    ->withoutOverlapping();