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

        $cmd_format = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'python3 format_for_lava_coloc.py --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
        $process_format = Process::forever()->run($cmd_format);
        Log::info("Full Docker command: " . $cmd_format);
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd_format . "\n");

        if ($params['coloc'] == 1) {
            $cmd_coloc = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'Rscript run_coloc.R --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
            $process_coloc = Process::forever()->run($cmd_coloc);
            Log::info("Full Docker command: " . $cmd_coloc);
            Storage::append($this->logfile, "Command to be executed:");
            Storage::append($this->logfile, $cmd_coloc . "\n");
        } else
        {
            Storage::append($this->logfile, "Colocalization analysis not selected.\n");
        }

        $cmd_lava = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'Rscript run_lava.R --filedir $job_location/ >>$job_location/job.log 2>>$job_location/error.log'";
        $process_lava = Process::forever()->run($cmd_lava);
        Log::info("Full Docker command: " . $cmd_lava);
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd_lava . "\n");

        $error = $process_format->exitCode();
        if ($error != 0) {
            JobHelper::JobTerminationHandling($jobID, 1, 'xqtls error occured');
            return;
        }

        SubmitJob::where('jobID', $jobID) 
        ->update([
            'status' => config('snp2gene_status_codes.15.short_name'),
            'completed_at' => date("Y-m-d H:i:s")
        ]);



    }

}