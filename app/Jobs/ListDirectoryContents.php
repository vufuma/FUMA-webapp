<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use App\Helpers\Helper;
use App\Helpers\JobHelper;
use App\Models\SubmitJob;
use Illuminate\Support\Facades\Storage;


class ListDirectoryContents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobID;
    protected $dir_name;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 28800; // 8 hours

    /**
     * Create a new job instance.
     */
    public function __construct($jobID)
    {
        $this->jobID = $jobID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // Update status when job is started
        $jobID = $this->jobID;
        SubmitJob::where('jobID', $jobID)
            ->update([
                'status' => 'RUNNING',
                'started_at' => date("Y-m-d H:i:s"),
                'uuid' => $this->job->uuid()
            ]);

        // define the job directories you want to look into for jobs
        $job_directories = [
            'jobs',
            'gene2func',
            'celltype'
        ];
        // For each job directory, get the jobIDs from the directory,
        // by getting the trailing part of the job paths which corresponds to the jobID
        $contents = [];
        foreach ($job_directories as $job_directory) {
            $dirs = array_map(function ($dir) use ($job_directory) {
                return [
                    'jobID' => substr($dir, strrpos($dir, '/') + 1), // get the trailing part of the job paths which is the jobID
                    'type' => ($job_directory == 'jobs') ? 'snp2gene' : $job_directory // keep also the type of the job as string
                ];
            }, Storage::directories(config('app.jobdir') . '/' . $job_directory)); // get jobIDs from the jobs directories 

            // merge the found directories into one array
            $contents = array_merge($contents, $dirs);
        }
        // flatten the array by the jobID key and keep the jobIDs in a separate array
        $contents = Helper::flattenArrayByNestedKey($contents, 'jobID', True);

        if ($contents) {
            // write the contents to a csv file
            $out_file = Storage::path(config('app.jobdir') . '/listDirectoryContents/' . $jobID . '/storage_jobs.csv');

            Helper::writeToCsv($out_file, $contents);
        }

        // DB entries
        // get all the jobIDs from the submit_jobs table
        $db_jobs = SubmitJob::whereNull('removed_at') // get only the jobs that are not removed
            ->wherein('type', ['snp2gene', 'gene2func', 'celltype', 'geneMap']) // remove the geneMap type after the first cleanup
            ->orderBy('created_at', 'desc')
            ->get([
                'jobID',
                'type',
                'status',
                'is_public'
            ])
            ->toArray();
        $db_jobs = Helper::flattenArrayByNestedKey($db_jobs, 'jobID', True);

        if ($db_jobs) {
            // write the contents to a csv file
            $out_file = Storage::path(config('app.jobdir') . '/listDirectoryContents/' . $jobID . '/db_jobs.csv');

            Helper::writeToCsv($out_file, $db_jobs);
        }

        JobHelper::JobTerminationHandling($jobID, 15);
        return;
    }

    public function failed($exception): void
    {
        if ($exception instanceof TimeoutExceededException) {
            JobHelper::JobTerminationHandling($this->jobID, 17);
        } else {
            JobHelper::JobTerminationHandling($this->jobID, 16);
        }
    }
}
