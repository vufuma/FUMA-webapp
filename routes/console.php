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

## Write a function to get the list of faulty jobs that are older than 1 month

function findFaultyJobs() {
    $err_indices = [
        0,
        1,
        2,
        3,
        4,
        5,
        6,
        7,
        8,
        9,
        10,
        11,
        12,
        13,
        14,
        16,
        17,
        18
    ];

    #make this in for loop to get the short names of the error codes
    $err_codes = array_map(function ($index) {
        return config('snp2gene_status_codes.' . $index . '.short_name');
    }, $err_indices);

    array_push(
        $err_codes,
        'ADMIN_KILLED',
        'ERROR',
        'PENDING',
        'NEW_geneMap',
        'JOB FAILED',
        'NEW' // since this will delete only jobs that are older than 3 months, stuck NEW jobs can also be deleted safely
    );

    $jobs = SubmitJob::wherein('status', $err_codes)
        ->where('created_at', '<', now()->subMonth(2))
        ->where('removed_at', null)
        ->get(['jobID', 'created_at', 'type', 'status']);

    return $jobs;
}

## Schedule a task to find the faulty jobs to be deleted
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.to_be_deleted.csv');
    $dir = config('app.jobdir');

    $jobs = findFaultyJobs();

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

    $jobs = findFaultyJobs();

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
Logic: for each user, OK jobs are removed if the user has more than a certain threshold. Currently set to 500. 
*/

function findOKJobs(){
    $emailsToSkip_file = Storage::path(config('app.jobdir') . '/schedule_logs/emails_to_skip_when_removing_ok_jobs.txt');
    $emailsToSkip = explode("\n", file_get_contents($emailsToSkip_file));

    $njobs = DB::table('SubmitJobs')
    ->selectRaw('count(*) as total, email')
    ->groupBy('email')
    ->having('total', '>', 500) #change here to update the maximum number of jobs per user to keep
    ->where('status', 'OK')
    ->where('type', 'snp2gene')
    ->where('removed_at', null)
    ->where('is_public', '=', 0)
    ->get(['jobID', 'created_at', 'type', 'status']);
    $results = [];

    foreach ($njobs as $njob) {
        $nToRemove = $njob->total - 500; #change here to update the maximum number of jobs per user to keep
        $allJobsPerEmail = DB::table('SubmitJobs')
        ->select('jobID', 'created_at', 'type', 'status', 'email')
        ->where('status', 'OK')
        ->where('type', 'snp2gene')
        ->where('removed_at', null)
        ->where('email', $njob->email)
        ->whereNot('email', $emailsToSkip)
        ->where('is_public', '=', 0)
        ->orderByRaw('created_at')
        ->limit($nToRemove)
        ->get();

        foreach ($allJobsPerEmail as $jobPerEmail) {
            $keys = array('jobID', 'created_at', 'type', 'status', 'email');
            $values = array($jobPerEmail->jobID, $jobPerEmail->created_at, $jobPerEmail->type, $jobPerEmail->status, $jobPerEmail->email);
            $jobPerEmail_arr = array_combine($keys, $values);
            array_push($results, $jobPerEmail_arr);
        }
    }
    return $results;
}

## Schedule a task to list the OK jobs to be deleted
Schedule::call(function(){
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.ok_jobs_to_be_deleted.csv');
    $results = findOKJobs();

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

    $jobs = findOKJobs();

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