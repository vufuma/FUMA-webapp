<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\TimeoutExceededException;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Process;
use App\Models\SubmitJob;
use JobHelper;

use App\CustomClasses\DockerApi\DockerNamesBuilder;

class CelltypeProcess implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $jobID;
    protected $logfile;
    protected $errorfile;

    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 28800; // 8 hours

    /**
     * Create a new job instance.
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
        // Update status when job is started
        $jobID = $this->jobID;
        $started_at = date("Y-m-d H:i:s");
        SubmitJob::where('jobID', $jobID)
            ->update([
                'status' => 'RUNNING',
                'started_at' => $started_at,
                'uuid' => $this->job->uuid()
            ]);

        $filedir = config('app.jobdir') . '/celltype/' . $jobID . '/';
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $this->logfile = $filedir . "job.log";
        $this->errorfile = $filedir . "error.log";

        // $script = scripts_path('magma_celltype.R');
        Storage::put($this->logfile, "----- magma_celltype.R -----\n");
        Storage::put($this->errorfile, "----- magma_celltype.R -----\n");

        $container_name = DockerNamesBuilder::containerName($jobID);
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'magma_celltype');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'cellType');

        $cmd = "docker run --rm --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'Rscript magma_celltype.R $job_location >>$job_location/job.log 2>>$job_location/error.log'";
        Storage::append($this->logfile, "Command to be executed:");
        Storage::append($this->logfile, $cmd . "\n");

        $process = Process::forever()->run($cmd);
        $error = $process->exitCode();

        if ($error != 0) {
            JobHelper::rmFiles($filedir);
            JobHelper::JobTerminationHandling($jobID, 18, 'CellType error occured');
            return;
        }

        JobHelper::rmFiles($filedir);
        JobHelper::JobTerminationHandling($jobID, 15);
        return;
    }

    public function failed($exception): void
    {
        JobHelper::kill_docker_containers_based_on_jobID($this->jobID);

        if ($exception instanceof TimeoutExceededException) {
            JobHelper::JobTerminationHandling($this->jobID, 17);
        } else {
            JobHelper::JobTerminationHandling($this->jobID, 16);
        }
    }
}
