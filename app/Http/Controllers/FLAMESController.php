<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use App\Jobs\FlamesProcess;
use App\CustomClasses\myFile;

use App\Models\SubmitJob;
use Illuminate\Support\Facades\Log;

use Helper;
use Auth;

class FLAMESController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Show the XTLS page
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.flames', ['status' => 'new', 'id' => 'none', 'page' => 'flames', 'prefix' => 'flames']);
    }

    public function getFLAMESHistory()
    {
        $user_id = Auth::user()->id;

        if ($user_id) {
            $queries = SubmitJob::with('parent:jobID,title,removed_at')
                ->where('user_id', $user_id)
                ->where('type', 'flames')
                ->whereNull('removed_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $queries = array();
        }

        return response()->json($queries);
    }


    public function viewJob($jobID)
    {
        $job = SubmitJob::where('jobID', $jobID)
            ->where('type', 'flames')
            ->whereNull('removed_at')
            ->first();
        if ($job == null) {
            return redirect('flames');
        }

        return view('pages.flames', ['status' => 'getJob', 'id' => $jobID, 'page' => 'flames', 'prefix' => 'flames']);
    }

    public function DTfile(Request $request)
    {
        $id = $request->input('jobID');
        $prefix = $request->input('prefix');
        $fin = $request->input('infile');
        $cols = $request->input('header');

        $file_path = config('app.jobdir') . '/' . $prefix . '/' . $id . '/' . $fin;

        return myFile::processCsvDataWithHeaders($file_path, $cols);
    }

    public function newJob(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;



        if ($request->filled("title")) {
            $title = $request->input('title');
        } else {
            $title = "None";
        }

        // Create new job in database
        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'flames';
        $submitJob->title = $title;
        $submitJob->status = 'NEW';
        $submitJob->save();
        $jobID = $submitJob->jobID;

        // Create new job on server

        $filedir = config('app.jobdir') . '/flames/' . $jobID;
        Storage::makeDirectory($filedir);
        Storage::putFileAs($filedir, $request->file('gwasSumstat'), 'input.gwas.gz');
        Storage::putFileAs($filedir, $request->file('preds'), 'input.preds');

        $snp2geneID = $request->input('s2gID');
        $sampleSize = $request->input('totalN');

        $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);
        $paramfile = $filedir . '/params.config';
 
        Storage::put($paramfile, "[jobinfo]");
        Storage::append($paramfile, "created_at=$date");
        Storage::append($paramfile, "title=$title");

        Storage::append($paramfile, "\n[version]");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA']);

        Storage::append($paramfile, "\n[params]");
        Storage::append($paramfile, "snp2geneID=$snp2geneID");
        Storage::append($paramfile, "sampleSize=$sampleSize");
        

        $this->queueNewJobs();

        return redirect("/flames#queryhistory");
    }

    public function queueNewJobs()
    {
        $user = Auth::user();
        $newJobs = (new SubmitJob)->getNewJobs_flames_only($user->id);

        $queue = 'default';
        if ($user->can('Access Priority Queue')) {
            $queue = 'high';
        }

        if (count($newJobs) > 0) {
            foreach ($newJobs as $job) {
                (new SubmitJob)->updateStatus($job->jobID, 'QUEUED');
                FlamesProcess::dispatch($user, $job->jobID)
                    ->onQueue($queue)
                    ->afterCommit();
            }
        }
        return;
    }

    public function deleteJob(Request $request)
    {
        $jobID = $request->input('jobID');
        return Helper::deleteJob(config('app.jobdir') . '/flames/', $jobID);
    }

    public function getS2GIDs()
    {
        $user_id = Auth::user()->id;
        $results = SubmitJob::where('user_id', $user_id)
            ->where('type', 'snp2gene')
            ->where('status', 'OK')
            ->whereNull('removed_at')
            ->get(['jobID', 'title']);
        return $results;
    }

    public function checkSNP2GENEFiles(Request $request)
    {
        $id = $request->input('jobID');
        if (Storage::exists(config('app.jobdir') . '/jobs/' . $id . "/magma.genes.raw") && Storage::exists(config('app.jobdir') . '/jobs/' . $id . "/magma.genes.out") && Storage::exists(config('app.jobdir') . '/jobs/' . $id . "/magma_exp_gtex_v8_ts_general_avg_log2TPM.gsa.out") && Storage::exists(config('app.jobdir') . '/jobs/' . $id . "/GenomicRiskLoci.txt")) {
            return 1;
        } else {
            return 0;
        }
    }

    public function downloadResults(Request $request)
    {
        $user = Auth::user();
        $code = $request->input('variant_code');
        $jobID = $request->input('jobID');
        $name = null;
        
        $job = SubmitJob::where('jobID', $jobID)
            ->where('type', 'flames')
            ->where('user_id', $user->id)
            ->whereNull('removed_at')
            ->first();
        
        if ($job == null) {
            return response()->json(['error' => 'You are not authorized to access this job'], 403);
        }
        
        switch ($code) {
            case "flamesResultsRaw":
                $name = "FLAMES_scores.raw";
                break;
            case "flamesResultsPred":
                $name = "FLAMES_scores_fmt.pred";
                break;
            default:
                return redirect()->back();
        }
        
        $downloadPath = config('app.abs_path_to_flames_jobs_on_host') . '/' . $jobID . '/' . $name;
        
        if (!file_exists($downloadPath)) {
        
        return response()->json([
            'error' => 'The requested file is not available.',
            'message' => 'The download file could not be found.'
        ], 404);
        }
        
        $headers = array('Content-Type: application');
        return response()->download($downloadPath, $name, $headers);
    }
}