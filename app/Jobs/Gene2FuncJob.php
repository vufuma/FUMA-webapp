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

class Gene2FuncJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobID;

    /**
     * Create a new queuable job instance.
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
        // Started so update status to RUNNING
        $jobID = $this->jobID;
        $started_at = date("Y-m-d H:i:s");
        SubmitJob::where('jobID', $jobID) 
            ->update([
                'status' => 'RUNNING',
                'started_at' => $started_at,
                'uuid' => $this->job->uuid()
            ]);

        $ref_data_path_on_host = config('app.ref_data_on_host_path');

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'g2f_r');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'gene2func');

        $cmd = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'Rscript gene2func.R $job_location/'";
        $process = Process::forever()->run($cmd);
        $error = $process->exitCode();
        if ($error != 0) {
            JobHelper::JobTerminationHandling($jobID, 1, 'Gene2Func step 1 error occured');
            return;
        }

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'g2f');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'gene2func');

        $cmd = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'python GeneSet.py $job_location/'";
        $process = Process::forever()->run($cmd);
        $error = $process->exitCode();
        if ($error != 0) {
            JobHelper::JobTerminationHandling($jobID, 2, 'Gene2Func step 2 error occured');
            return;
        }
        SubmitJob::where('jobID', $jobID) 
            ->update([
                'status' => config('snp2gene_status_codes.15.short_name'),
                'completed_at' => date("Y-m-d H:i:s")
            ]);
        Log::info("Saved Gene2Func job id: ".$jobID);
        return;
    }

}