<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Jobs\ListDirectoryContents;
use App\Jobs\DelDirectoryAndDbContents;
use App\Models\SubmitJob;

use Auth;

class SyncDbAndStorageController extends Controller
{
    public function index()
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

    public function newListingJob()
    {
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
