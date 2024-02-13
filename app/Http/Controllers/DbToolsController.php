<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Jobs\ListDirectoryContents;
use App\Jobs\DelDirectoryAndDbContents;
use App\Models\SubmitJob;
use App\Helpers\Helper;

use Auth;


class DbToolsController extends Controller
{
    public function index()
    {
        return view('admin.dbtools.dbtools');
    }

    public function syncDbStorage()
    {
        return view('admin.dbtools.syncdbstorage');
    }

    public function del(Request $request)
    {
        $dirs = [];
        $db_entries = [];

        $validated = $request->validate([
            'selected_listing_jobID' => 'required|int',
            'dirs' => 'required_without_all:db_entries|min:1',
            'db_entries' => 'required_without_all:dirs|min:1'
        ]);

        if (isset($validated['dirs'])) {
            $dirs = $validated['dirs'];
        }
        if (isset($validated['db_entries'])) {
            $db_entries = $validated['db_entries'];
        }

        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;


        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'delDirectoryAndDbContents';
        $submitJob->title = 'Delete job directories and db entries';
        $submitJob->status = 'NEW';
        $submitJob->parent_id = $validated['selected_listing_jobID'];
        $submitJob->save();
        $jobID = $submitJob->jobID;

        // // create job directory
        $filedir = config('app.jobdir') . '/delDirectoryAndDbContents/' . $jobID;
        Storage::makeDirectory($filedir);

        (new SubmitJob)->updateStatus($jobID, 'QUEUED');
        DelDirectoryAndDbContents::dispatch($jobID, $dirs, $db_entries)->afterCommit();

        // return redirect('/admin/db-tools/sync-db-storage');

        return redirect()->back();
    }

    private function getDbEntries()
    {

        // define the job directories you want to look into for jobs
        $job_directories = [
            'jobs',
            'gene2func',
            'celltype'
        ];

        // get all the jobIDs from the submit_jobs table
        $db_jobs = SubmitJob::whereNull('removed_at')
            ->orderBy('created_at', 'desc')
            ->get([
                'jobID',
                'type',
                'status',
                'is_public'
            ])
            ->toArray();
        $db_jobs = Helper::flattenArrayByNestedKey($db_jobs, 'jobID', True);
        $db_jobs_ids = array_keys($db_jobs);

        // For each job directory, get the jobIDs from the directory,
        // by getting the trailing part of the job paths which corresponds to the jobID
        $found_dirs = [];
        foreach ($job_directories as $job_directory) {
            $dirs = array_map(function ($dir) use ($job_directory) {
                return [
                    'jobID' => substr($dir, strrpos($dir, '/') + 1), // get the trailing part of the job paths which is the jobID
                    'type' => ($job_directory == 'jobs') ? 'snp2gene' : $job_directory // keep also the type of the job as string
                ];
            }, Storage::directories(config('app.jobdir') . '/' . $job_directory)); // get jobIDs from the jobs directories 

            // merge the found directories into one array
            $found_dirs = array_merge($found_dirs, $dirs);
        }
        // flatten the array by the jobID key and keep the jobIDs in a separate array
        $found_dirs = Helper::flattenArrayByNestedKey($found_dirs, 'jobID', True);
        $found_dirs_ids = array_keys($found_dirs);

        // compare the jobIDs from the database with the jobIDs from the directories
        $dirs_not_in_db = array_diff($found_dirs_ids, $db_jobs_ids);
        $jobs_not_in_dir = array_diff($db_jobs_ids, $found_dirs_ids);

        // if there are no jobs to sync, redirect to the dbtools index page with a status message
        if (empty($dirs_not_in_db) && empty($jobs_not_in_dir)) {
            return redirect()->action([DbToolsController::class, 'index'])->with(['status' => 'No jobs to sync.']);
        }

        // if there are jobs to sync, show the sync page
        return view('admin.dbtools.syncdbstorage', [
            'dirs_not_in_db' => array_intersect_key($found_dirs, array_flip($dirs_not_in_db)),
            'jobs_not_in_dir' => array_slice(array_intersect_key($db_jobs, array_flip($jobs_not_in_dir)), 0, 10), // only show the first 10, remove this before production
        ]);
    }

    public function newListingJob()
    {
        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;


        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'listDirectoryContents';
        $submitJob->title = 'List job directories contents';
        $submitJob->status = 'NEW';
        $submitJob->save();
        $jobID = $submitJob->jobID;

        // create job directory
        $filedir = config('app.jobdir') . '/listDirectoryContents/' . $jobID;
        Storage::makeDirectory($filedir);

        (new SubmitJob)->updateStatus($jobID, 'QUEUED');
        ListDirectoryContents::dispatch($jobID)->afterCommit();

        return redirect('/admin/db-tools/sync-db-storage');
    }
}
