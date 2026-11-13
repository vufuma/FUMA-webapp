<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SubmitJob;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;
use App\Helpers\JobHelper;
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

/* Scheduling deletion of faulty jobs
Logic: faulty jobs are removed if they are older than 1 month
*/

## Schedule a task to find the faulty jobs to be deleted
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.faulty_jobs_to_be_deleted.csv');
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
## Currently there is a delay between the listing of faulty jobs and their deletion, so there are more jobs that are deleted than being listed as above because of the timestamp
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.faulty_jobs_deleted.csv');
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

/* Schedulilng deletion of OK jobs 
Logic: OK jobs are removed per user if the user has more than a threshold of OK jobs. The threshold is set in the function findOKJobs and is currently set to 100. 
*/

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

/* Schedulilng deletion of input gwas summary statistics and intermediate files created from input gwas summary statistics
Logic: starting FUMA v2.0.0, users can choose to keep the input gwas summary statistics and intermediate files created from input gwas summary statistics for 7 days to run FLAMES. After 7 days, these files will be deleted. 
*/
## Schedule a task to delete the gwas sumstat after a week
Schedule::call(function () {

    $jobs = Helper::findJobsToDeleteInput();

    foreach ($jobs as $job) {
        JobHelper::rmFiles($job);
    }
})->weeklyOn(4, '16:00')
    ->environments('production')
    ->name('Delete input gwas')
    ->withoutOverlapping();

/* Schedulilng deletion of SNP2GENE jobs based on timestamp 
Logic: SNP2GENE jobs are removed if they are older than a certain timestamp. At the update around May 2026, this is set to prior to 01-01-2023. 
*/
# Schedule a task to list the SNP2GENE jobs to be deleted based on timestamp
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.to_be_deleted_createdbefore20230101.csv');
    $dir = config('app.jobdir');

    $jobs = Helper::findSNP2GENEJobsTimestamp();

    $results = [];
    foreach ($jobs as $job) {

        array_push($results, $job->toArray());
    }

    if (count($results) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $results);
})->yearlyOn(1, 1, '17:00')
    ->environments('production')
    ->name('Find SNP2GENE jobs to be deleted based on timestamp')
    ->withoutOverlapping();

# Schedule a task to delete the SNP2GENE jobs based on timestamp
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.createdbefore20230101.deleted.csv');
    $dir = config('app.jobdir');

    $jobs = Helper::findSNP2GENEJobsTimestamp();

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
})->yearlyOn(1, 1, '17:00')
    ->environments('production')
    ->name('Delete SNP2GENE jobs to be deleted based on timestamp')
    ->withoutOverlapping();