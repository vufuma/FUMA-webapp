<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\SubmitJob;
use App\Helpers\Helper;

class SyncdbstorageJobs extends Component
{
    public $jobs;

    public $selected_jobs = [];

    public $shown_job = null;
    public $dirs_not_in_db;
    public $jobs_not_in_dir;

    public function render()
    {
        return view('livewire.syncdbstorage-jobs');
    }

    public function mount()
    {
        $this->getJobs();
    }

    public function getJobs()
    {
        $this->jobs = SubmitJob::wherein('type', ['listDirectoryContents', 'delDirectoryAndDbContents'])
            ->whereNull('removed_at')
            ->orderBy('created_at', 'desc')
            ->with('user:id,email')
            ->get();
    }

    public function delJobs()
    {
        $validated = $this->validate([
            'selected_jobs' => 'required|min:1',
        ]);

        foreach ($validated['selected_jobs'] as $validate_job_id) {
            $this->deleteJob($validate_job_id);
        }

        return $this->redirect('/admin/db-tools/sync-db-storage');
    }

    private function deleteJob($jobID)
    {
        $type = SubmitJob::where('jobID', $jobID)->value('type');

        if ($type == 'listDirectoryContents') {
            return Helper::deleteJob(config('app.jobdir') . '/listDirectoryContents/', $jobID);
        } elseif ($type == 'delDirectoryAndDbContents') {
            return Helper::deleteJob(config('app.jobdir') . '/delDirectoryAndDbContents/', $jobID);
        }
    }

    public function showSelected()
    {
        $validated = $this->validate([
            'selected_jobs' => 'required|min:1|max:1', //TODO: change return message
        ]);

        $selected_job = SubmitJob::find($validated['selected_jobs'][0]);

        if ($selected_job->status == 'OK' && $selected_job->type == 'listDirectoryContents') {
            $this->shown_job = $selected_job->jobID;

            // read the job file and get the job details
            $result = Helper::getFilesContents(config('app.jobdir') . '/listDirectoryContents/' . $selected_job->jobID . '/', ['storage_jobs.csv', 'db_jobs.csv']);

            $db_jobs = Helper::flattenArrayByNestedKey($result['db_jobs.csv'], 'jobID', True);
            $db_jobs_ids = array_keys($db_jobs);

            $storage_jobs = Helper::flattenArrayByNestedKey($result['storage_jobs.csv'], 'jobID', True);
            $storage_jobs_ids = array_keys($storage_jobs);

            // compare the jobIDs from the database with the jobIDs from the directories
            $dirs_not_in_db = array_diff($storage_jobs_ids, $db_jobs_ids);
            $jobs_not_in_dir = array_diff($db_jobs_ids, $storage_jobs_ids);

            // if there are no jobs to sync, redirect to the dbtools index page with a status message
            if (empty($dirs_not_in_db) && empty($jobs_not_in_dir)) {
                return redirect('/admin/db-tools/sync-db-storage')->with(['status' => 'No jobs to sync.']);
            }

            $this->dirs_not_in_db = array_intersect_key($storage_jobs, array_flip($dirs_not_in_db));
            $this->jobs_not_in_dir = array_intersect_key($db_jobs, array_flip($jobs_not_in_dir)); // only show the first 10, remove this before production
        } else {
            $this->shown_job = null;
        }
    }

    public function clearSelection()
    {
        $this->shown_job = null;
    }
}
