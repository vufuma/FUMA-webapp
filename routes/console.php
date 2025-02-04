<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Models\SubmitJob;
use Illuminate\Support\Facades\Storage;
use App\Helpers\Helper;

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

# schedule a task to list faulty jobs 
Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.to_be_deleted.csv');
    $dir = config('app.jobdir');
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
        ->where('created_at', '<', now()->subMonth(3))
        ->where('removed_at', null)
        ->get(['jobID', 'created_at', 'type', 'status']);

    $result = [];
    foreach ($jobs as $job) {

        array_push($result, $job->toArray());
    }

    if (count($result) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $result);
})->weeklyOn(2, '10:45')
    ->environments('production')
    ->name('Find jobs to be deleted')
    ->withoutOverlapping();


Schedule::call(function () {
    $out_file = Storage::path(config('app.jobdir') . '/schedule_logs/' . date('Y-m-d_H-i-s') . '.schedule.csv');
    $dir = config('app.jobdir');
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
        ->where('created_at', '<', now()->subMonth(3))
        ->where('removed_at', null)
        ->get(['jobID', 'created_at', 'type', 'status']);

    $result = [];
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

        array_push($result, $job->toArray());
    }

    if (count($result) == 0) {
        return;
    }
    Helper::writeToCsv($out_file, $result);
})->weeklyOn(3, '20:00')
    ->environments('production')
    ->name('Delete Faulty Jobs')
    ->withoutOverlapping();
