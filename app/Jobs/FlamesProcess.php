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
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use Illuminate\Support\Facades\Process;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class FlamesProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobID;

    /**
     * Create a new queuable job instance.
     */
    public function __construct($user, $jobID)
    {
        $this->user = $user;
        $this->jobID = $jobID;
    }

    /**
     * Execute the job.
     */
    public function handle(): void 
    {   
        $jobID = $this->jobID;
        $filedir = config('app.jobdir') . '/flames/' . $jobID . '/';
        $this->logfile = $filedir . "job.log";
        $this->errorfile = $filedir . "error.log";
        
        // Started so update status to RUNNING
        $started_at = date("Y-m-d H:i:s");
        SubmitJob::where('jobID', $jobID) 
            ->update([
                'status' => 'RUNNING',
                'started_at' => $started_at,
                'uuid' => $this->job->uuid()
            ]);

        
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $params = parse_ini_string(Storage::get($filedir . "params.config"), false, INI_SCANNER_RAW);

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma-js', 'flames');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'flames');
        $s2g_dir = config('app.abs_path_to_jobs_on_host');

        #######################################################################
        # Run flames
        #######################################################################
        Storage::append($this->logfile, "INFO: Starting finemapping " . date("Y-m-d H:i:s") . "\n");
        $cmd_format = "docker run --rm --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'python run_flames.py --filedir $job_location --s2gdir $s2g_dir >>$job_location/job.log 2>>$job_location/error.log'";
        $tmp = Process::forever()->run($cmd_format);
        Log::info("Full Docker command: " . $cmd_format);
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd_format . "\n");

        $tmpError = $tmp->exitCode();

        // Log the exit code
        Storage::append($this->logfile, "Process exit code: " . $tmpError . "\n");
        if ($tmpError == 1) {
            JobHelper::JobTerminationHandling($jobID, 31, 'An error occurs at tabixing the input GWAS file.');
            return;
        } elseif ($tmpError == 2) {
            JobHelper::JobTerminationHandling($jobID, 32, 'An error occurs at subsetting variants per locus');
            return;
        }elseif ($tmpError != 0) {
            JobHelper::JobTerminationHandling($jobID, 33, 'An error occurs at flames');
            return;
        } 
        
        // Completed successfully
        SubmitJob::where('jobID', $jobID) 
        ->update([
            'status' => config('all_status_codes.15.short_name'),
            'completed_at' => date("Y-m-d H:i:s")
        ]);



    }

}