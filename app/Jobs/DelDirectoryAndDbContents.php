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


class DelDirectoryAndDbContents implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $jobID;
    protected $dirs;
    protected $db_entries;
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 28800; // 8 hours

    /**
     * Create a new job instance.
     */
    public function __construct($jobID, $dirs, $db_entries)
    {
        $this->jobID = $jobID;
        $this->dirs = $dirs;
        $this->db_entries = $db_entries;
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
        // print_r($this->dirs);
        // print_r($this->db_entries);

        // delete directories
        if (!empty($this->dirs)) {
            $result = $this->deleteDirs($this->dirs);
        }

        // delete db entries
        if (!empty($this->db_entries)) {
            $result = $this->deleteDbEntries($this->db_entries);
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

    private function deleteDirs($dirs)
    {
        // $filedir = config('app.jobdir') . '/delDirectoryAndDbContents/' . $this->jobID . '/';
        // $result = [
        //     'deletion_status' => False,
        //     'message' => ''
        // ];

        // foreach ($dirs as $dir) {
        //     $result = Helper::deleteJobDirectoryOnly($filedir, $dir);
        //     if ($result['deletion_status'] == False) {
        //         return $result;
        //     }
        // }

        // return $result;
    }

    private function deleteDbEntries($db_entries)
    {
        // $result = [
        //     'deletion_status' => False,
        //     'message' => ''
        // ];

        // foreach ($db_entries as $db_entry) {
        //     $result = Helper::deleteJobDbEntryOnly($db_entry);
        //     if ($result['deletion_status'] == False) {
        //         return $result;
        //     }
        // }

        // return $result;
    }
}
