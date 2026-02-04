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

class XqtlsProcess implements ShouldQueue
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
        // Started so update status to RUNNING
        $jobID = $this->jobID;
        $started_at = date("Y-m-d H:i:s");
        SubmitJob::where('jobID', $jobID) 
            ->update([
                'status' => 'RUNNING',
                'started_at' => $started_at,
                'uuid' => $this->job->uuid()
            ]);

        $filedir = config('app.jobdir') . '/xqtls/' . $jobID . '/';
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $params = parse_ini_string(Storage::get($filedir . "params.config"), false, INI_SCANNER_RAW);
        $this->logfile = $filedir . "job.log";
        $this->errorfile = $filedir . "error.log";

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma-js', 'xqtls');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'xqtls');

        # Input checking and formatting of the input gwas sumstat for the locus
        Storage::append($this->logfile, "INFO: Starting processing of input gwas summary statistics for the locus at " . date("Y-m-d H:i:s") . "\n");
        $cmd_format = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'python3 process_locus.py --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
        $process_locus = Process::forever()->run($cmd_format);
        Log::info("Full Docker command: " . $cmd_format);
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd_format . "\n");

        $locusError = $process_locus->exitCode();

        // Log the exit code
        Storage::append($this->logfile, "Process input gwas sumstat exit code: " . $locusError . "\n");
        if ($locusError == 1) {
            JobHelper::JobTerminationHandling($jobID, 23, 'Incorrect header format for the input gwas summary statistics for the locus');
            return;
        } elseif ($locusError != 0) {
            JobHelper::JobTerminationHandling($jobID, 24, 'An error occurs when processing the input gwas summary statistics for the locus');
            return;
        } 

        # Format the xQTL datasets for LAVA and colocalization
        Storage::append($this->logfile, "INFO: Starting formatting of xQTL datasets for LAVA and colocalization at " . date("Y-m-d H:i:s") . "\n");
        $cmd_format = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'python3 format_for_lava_coloc.py --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
        $process_qtls = Process::forever()->run($cmd_format);
        Log::info("Full Docker command: " . $cmd_format);
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd_format . "\n");

        $processQtlError = $process_qtls->exitCode();

        // Log the exit code
        Storage::append($this->logfile, "Format xQTL datasets process exit code: " . $processQtlError . "\n");
        if ($processQtlError != 0) {
            JobHelper::JobTerminationHandling($jobID, 25, 'An error occurs when formatting the xQTL datasets for LAVA and colocalization');
            return;
        }

        # Run colocalization if selected
        if ($params['coloc'] == 1) {
            Storage::append($this->logfile, "Colocalization analysis started.\n");
            $cmd_coloc = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'Rscript run_coloc.R --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
            $process_coloc = Process::forever()->run($cmd_coloc);
            Log::info("Full Docker command: " . $cmd_coloc);
            Storage::append($this->logfile, "Command to be executed:");
            Storage::append($this->logfile, $cmd_coloc . "\n");

            $colocError = $process_coloc->exitCode();

            // Log the exit code
            Storage::append($this->logfile, "Colocalization process exit code: " . $colocError . "\n");
    

            if ($colocError == 1) {
                // Tissue name not found in sample size lookup table
                JobHelper::JobTerminationHandling($jobID, 21, 'xqtls colocalization could not be performed due to tissue name not found in sample size lookup table.');
                return;
            } elseif ($colocError == 2) {
                // No genes found in the locus
                JobHelper::JobTerminationHandling($jobID, 22, 'xqtls colocalization could not be performed due to no genes found in the locus.');
                return;
            } elseif ($colocError != 0) {
                // Log error output for debugging
                Storage::append($this->logfile, "Error output: " . $process_coloc->errorOutput() . "\n");
                JobHelper::JobTerminationHandling($jobID, 19, 'xqtls error occured');
                return;
            }

        } else
        {
            Storage::append($this->logfile, "Colocalization analysis not selected.\n");
        }

        if ($params['lava'] == 1) {
            Storage::append($this->logfile, "LAVA analysis started.\n");
            $cmd_lava = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'Rscript run_lava.R --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
            $process_lava = Process::forever()->run($cmd_lava);
            Log::info("Full Docker command: " . $cmd_lava);
            Storage::append($this->logfile, "Command to be executed:");
            Storage::append($this->logfile, $cmd_lava . "\n");


            $lavaError = $process_lava->exitCode();
            if ($lavaError != 0) {
                JobHelper::JobTerminationHandling($jobID, 20, 'xqtls error occured');
                return;
            }

        } else
        {
            Storage::append($this->logfile, "LAVA analysis not selected.\n");
        }
        
        // Completed successfully
        SubmitJob::where('jobID', $jobID) 
        ->update([
            'status' => config('all_status_codes.15.short_name'),
            'completed_at' => date("Y-m-d H:i:s")
        ]);



    }

}