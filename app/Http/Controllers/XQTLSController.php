<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use App\Jobs\XqtlsProcess;
use App\CustomClasses\myFile;

use App\Models\SubmitJob;
use Illuminate\Support\Facades\Log;

use Helper;
use Auth;

class XQTLSController extends Controller
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
        return view('pages.xqtls', ['status' => 'new', 'id' => 'none', 'page' => 'xqtls', 'prefix' => 'xqtls']);
    }


    public function getQTLSHistory()
    {
        $user_id = Auth::user()->id;

        if ($user_id) {
            $queries = SubmitJob::with('parent:jobID,title,removed_at')
                ->where('user_id', $user_id)
                ->where('type', 'xqtls')
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
            ->where('type', 'xqtls')
            ->whereNull('removed_at')
            ->first();
        if ($job == null) {
            return redirect('xqtls');
        }

        return view('pages.xqtls', ['status' => 'getJob', 'id' => $jobID, 'page' => 'xqtls', 'prefix' => 'xqtls']);
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

        // get xQTLs datasets
        $xqtlsDatasets = $this->joinQTLdatasets(
            $this->parseQtl($request->input('eqtlGtexv10Ds')),
            $this->parseQtl($request->input('pqtl9Sun2023Ds')) #TODO: add 
            // $this->parseQtl($request->input('pqtl9Sun2023Ds'))
            // $this->parseQtl($request->input('eqtlCatalog')),
            // $this->parseQtl($request->input('sqtlGtexv10')),
            // $this->parseQtl($request->input('apaqtlGtexv10')),
            // $this->parseQtl($request->input('pqtls'))
        );



        if ($request->filled("title")) {
            $title = $request->input('title');
        } else {
            $title = "None";
        }

        // Create new job in database
        $submitJob = new SubmitJob;
        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'xqtls';
        $submitJob->title = $title;
        $submitJob->status = 'NEW';
        $submitJob->save();
        $jobID = $submitJob->jobID;

        // Create new job on server

        $filedir = config('app.jobdir') . '/xqtls/' . $jobID;
        Storage::makeDirectory($filedir);
        Storage::putFileAs($filedir, $request->file('locusSumstat'), 'locus.input.orig');

        $build = $request->input('build');

        $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);
        $paramfile = $filedir . '/params.config';

        $chrom = $request->input('chrom');
        $locusStart = $request->input('locusStart');
        $locusEnd = $request->input('locusEnd');
        
        if ($request->filled('coloc')) {
            $coloc = 1;
            $pp4 = $request->input('pp4');
            if ($request->filled('colocGene')) {
                $colocGene = $request->input('colocGene');
            } else {
                $colocGene = "all";
            }
        } else {
            $coloc = 0;
            $pp4 = "NA";
            $colocGene = "NA";
        }

        
        if ($request->filled('lava')) {
            $lava = 1;
            $phenotype = $request->input('phenotype');
            if ($request->filled('lavaGene')) {
                $lavaGene = $request->input('lavaGene');
            } else {
                $lavaGene = "all";
            }
        } else {
            $lava = 0;
            $phenotype = "NA";
            $lavaGene = "NA";
        }
            
        $cases = $request->input('cases');
        $totalN = $request->input('totalN');
        
 
        Storage::put($paramfile, "[jobinfo]");
        Storage::append($paramfile, "created_at=$date");
        Storage::append($paramfile, "title=$title");

        Storage::append($paramfile, "\n[version]");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA']);

        Storage::append($paramfile, "\n[params]");
        Storage::append($paramfile, "build=$build");
        Storage::append($paramfile, "chrom=$chrom");
        Storage::append($paramfile, "start=$locusStart");
        Storage::append($paramfile, "end=$locusEnd");
        Storage::append($paramfile, "coloc=$coloc");
        Storage::append($paramfile, "pp4=$pp4");
        Storage::append($paramfile, "colocGene=$colocGene");
        Storage::append($paramfile, "lava=$lava");
        Storage::append($paramfile, "phenotype=$phenotype");
        Storage::append($paramfile, "lavaGene=$lavaGene");
        Storage::append($paramfile, "cases=$cases");
        Storage::append($paramfile, "totalN=$totalN");
        Storage::append($paramfile, "datasets=$xqtlsDatasets");
        

        $this->queueNewJobs();

        return redirect("/xqtls#queryhistory");
    }

    private function joinQTLdatasets(...$qtlArrays) 
    {
        $parts = [];

        foreach ($qtlArrays as $array) {
            if (!empty($array) && is_array($array)) {
                $parts[] = implode(":", $array);
            }
        }

        return !empty($parts) ? implode(":", $parts) : "NA";
    }

    private function parseQtl($temp) {
        $qtlMapTs = [];
        
        if (!is_array($temp)) {
            return $qtlMapTs;
        }
        
        foreach ($temp as $ts) {
            if ($ts != "null") {
                $qtlMapTs[] = $ts;
            }
        }
        return $qtlMapTs;
    }

    public function queueNewJobs()
    {
        $user = Auth::user();
        $newJobs = (new SubmitJob)->getNewJobs_xqtls_only($user->id);

        $queue = 'default';
        if ($user->can('Access Priority Queue')) {
            $queue = 'high';
        }

        if (count($newJobs) > 0) {
            foreach ($newJobs as $job) {
                (new SubmitJob)->updateStatus($job->jobID, 'QUEUED');
                XqtlsProcess::dispatch($user, $job->jobID)
                    ->onQueue($queue)
                    ->afterCommit();
            }
        }
        return;
    }

    public function deleteJob(Request $request)
    {
        $jobID = $request->input('jobID');
        return Helper::deleteJob(config('app.jobdir') . '/xqtls/', $jobID);
    }

    public function downloadResults(Request $request)
    {
        $user = Auth::user();
        $code = $request->input('variant_code');
        $jobID = $request->input('jobID');
        $name = null;
        
        $job = SubmitJob::where('jobID', $jobID)
            ->where('type', 'xqtls')
            ->where('user_id', $user->id)
            ->whereNull('removed_at')
            ->first();
        
        if ($job == null) {
            return response()->json(['error' => 'You are not authorized to access this job'], 403);
        }
        
        switch ($code) {
            case "colocResultsFull":
                $name = "coloc_results.txt";
                break;
            case "colocResultsFiltered":
                $name = "coloc_results_filtered.txt";
                break;
            case "lavaResultsFull":
                $name = "lava_bivar_results_all_datasets.txt";
                break;
            case "lavaResultsFiltered":
                $name = "lava_bivar_results_all_datasets_significant.txt";
                break;
            default:
                return redirect()->back();
        }
        
        $downloadPath = config('app.abs_path_to_xqtls_jobs_on_host') . '/' . $jobID . '/' . $name;
        
        if (!file_exists($downloadPath)) {
        
        return response()->json([
            'error' => 'The requested file is not available.',
            'message' => 'The download file could not be found. This could be because coloc and/or LAVA was not selected or because there were no results.'
        ], 404);
}
        
        $headers = array('Content-Type: application');
        return response()->download($downloadPath, $name, $headers);
    }
}