<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use App\Jobs\Gene2FuncJob;
use App\CustomClasses\myFile;

use App\Models\SubmitJob;
use Illuminate\Support\Facades\Log;

use Helper;
use Auth;

class G2FController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('pages.gene2func', ['status' => 'new', 'id' => 'none', 'page' => 'gene2func', 'prefix' => 'gene2func']);
    }

    public function viewJob($jobID)
    {
        $job = SubmitJob::where('jobID', $jobID)
            ->where('type', 'gene2func')
            ->whereNull('removed_at')
            ->first();
        if ($job == null) {
            return redirect('gene2func');
        }

        return view('pages.gene2func', ['status' => 'getJob', 'id' => $jobID, 'page' => 'gene2func', 'prefix' => 'gene2func']);
    }

    public function getJobList()
    {
        $user_id = Auth::user()->id;

        if ($user_id) {
            $queries = SubmitJob::with('parent:jobID,title,removed_at')
                ->where('user_id', $user_id)
                ->where('type', 'gene2func')
                ->whereNull('removed_at')
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            $queries = array();
        }

        return response()->json($queries);
    }

    public function deleteJob(Request $request)
    {
        $jobID = $request->input('jobID');
        return Helper::deleteJob(config('app.jobdir') . '/gene2func/', $jobID);
    }

    public function gene2funcSubmit(Request $request)
    {
        $date = date('Y-m-d H:i:s');
        $email = Auth::user()->email;
        $user_id = Auth::user()->id;

        if ($request->filled('title')) {
            $title = $request->input('title');
        } else {
            $title = "None";
        }

        $submitJob = new SubmitJob();

        $submitJob->email = $email;
        $submitJob->user_id = $user_id;
        $submitJob->type = 'gene2func';
        $submitJob->title = $title;
        $submitJob->status = 'NEW';
        $submitJob->save();

        $jobID = $submitJob->jobID;

        $filedir = config('app.jobdir') . '/gene2func/' . $jobID;
        Storage::makeDirectory($filedir);
        $filedir = $filedir . '/';

        if ($request->filled('genes')) {
            $gtype = "text";
            $gval = $request->input('genes');
            $gval = preg_split('/[\n\r]+/', $gval);
            $gval = implode(':', $gval);
        } else {
            $gtype = "file";
            $gval = $_FILES["genesfile"]["name"];
            myFile::fileValidationAndStore($request->file('genesfile'), $gval, $filedir);
        }

        if ($request->filled('genetype')) {
            $bkgtype = "select";
            $bkgval = $request->input('genetype');
            $bkgval = implode(':', $bkgval);
        } else if ($request->filled('bkgenes')) {
            $bkgtype = "text";
            $bkgval = $request->input('bkgenes');
            $bkgval = preg_split('/[\n\r]+/', $bkgval);
            $bkgval = implode(':', $bkgval);
        } else {
            $bkgtype = "file";
            $bkgval = $_FILES["bkgenesfile"]["name"];
            myFile::fileValidationAndStore($request->file('bkgenesfile'), $bkgval, $filedir);
        }

        $ensembl = $request->input('ensembl');

        $gsFileN = (int)$request->input('gsFileN');
        $gsFiles = "NA";
        if ($gsFileN > 0) {
            $gsFiles = [];
            $n = 1;
            while (count($gsFiles) < $gsFileN) {
                $id = (string) $n;
                if ($request->hasFile("gsFile" . $id)) {
                    $tmp_filename = $_FILES["gsFile" . $id]["name"];
                    myFile::fileValidationAndStore($request->file("gsFile" . $id), $tmp_filename, $filedir);
                    $gsFiles[] = $tmp_filename;
                }
                $n++;
            }
            $gsFiles = implode(":", $gsFiles);
        }

        $gene_exp = implode(":", $request->input("gene_exp"));

        if ($request->filled('MHC')) {
            $MHC = 1;
        } else {
            $MHC = 0;
        }

        $adjPmeth = $request->input('adjPmeth');
        $adjPcut = $request->input('adjPcut');
        $minOverlap = $request->input('minOverlap');

        $app_config = parse_ini_file(Helper::scripts_path('app.config'), false, INI_SCANNER_RAW);
        // write parameters to config file
        $paramfile = $filedir . 'params.config';
        Storage::put($paramfile, "[jobinfo]");
        Storage::append($paramfile, "created_at=$date");
        Storage::append($paramfile, "title=$title");

        Storage::append($paramfile, "\n[version]");
        Storage::append($paramfile, "FUMA=" . $app_config['FUMA']);
        Storage::append($paramfile, "MsigDB=" . $app_config['MsigDB']);
        Storage::append($paramfile, "WikiPathways=" . $app_config['WikiPathways']);
        Storage::append($paramfile, "GWAScatalog=" . $app_config['GWAScatalog']);

        Storage::append($paramfile, "\n[params]");
        Storage::append($paramfile, "gtype=$gtype");
        Storage::append($paramfile, "gval=$gval");
        Storage::append($paramfile, "bkgtype=$bkgtype");
        Storage::append($paramfile, "bkgval=$bkgval");
        Storage::append($paramfile, "ensembl=$ensembl");
        Storage::append($paramfile, "gsFileN=$gsFileN");
        Storage::append($paramfile, "gsFiles=$gsFiles");
        Storage::append($paramfile, "gene_exp=$gene_exp");
        Storage::append($paramfile, "MHC=$MHC");
        Storage::append($paramfile, "adjPmeth=$adjPmeth");
        Storage::append($paramfile, "adjPcut=$adjPcut");
        Storage::append($paramfile, "minOverlap=$minOverlap");

        $queue = 'default';
        $user = Auth::user();
        if ($user->can('Access Priority Queue')) {
            $queue = 'high';
        }
        // Queue job
        (new SubmitJob)->updateStatus($jobID, 'QUEUED');
        Gene2FuncJob::dispatch($jobID)
            ->onQueue($queue)
            ->afterCommit();

        $data = [
            'id' => $jobID,
            'filedir' => $filedir,
            'gtype' => $gtype,
            'gval' => $gval,
            'bkgtype' => $bkgtype,
            'bkgval' => $bkgval,
            'ensembl' => $ensembl,
            'gene_exp' => $gene_exp,
            'MHC' => $MHC,
            'adjPmeth' => $adjPmeth,
            'adjPcut' => $adjPcut,
            'minOverlap' => $minOverlap
        ];

        return view('pages.gene2func', ['status' => 'query', 'id' => $jobID, 'page' => 'queryhistory', 'prefix' => 'gene2func', 'data' => $data]);
    }

    public function snp2geneGeneQuery(Request $request)
    {
        $s2gID = $request->input('jobID');

        $checkExists = SubmitJob::where('parent_id', $s2gID)
            ->where('type', 'gene2func')
            ->whereNull('removed_at')
            ->first();
        if ($checkExists == null) {
            $date = date('Y-m-d H:i:s');
            $email = Auth::user()->email;
            $user_id = Auth::user()->id;

            if ($request->filled('title')) {
                $title = $request->input('title');
            } else {
                $title = "None";
            }

            $s2gTitle = SubmitJob::find($s2gID)->title;

            $submitJob = new SubmitJob();
            $submitJob->email = $email;
            $submitJob->user_id = $user_id;
            $submitJob->parent_id = $s2gID;
            $submitJob->type = 'gene2func';
            $submitJob->title = $title;
            $submitJob->status = 'NEW';
            $submitJob->save();

            // Get jobID (automatically generated)
            $jobID = $submitJob->jobID;
            $filedir = config('app.jobdir') . '/gene2func/' . $jobID;
            Storage::makeDirectory($filedir);
            $filedir = $filedir . '/';

            $s2gfiledir = config('app.jobdir') . '/jobs/' . $s2gID . '/';
            $gtype = "text";
            $bkgtype = "select";
            $params = parse_ini_string(Storage::get($s2gfiledir . 'params.config'), false, INI_SCANNER_RAW);
            $ensembl = $params['ensembl'];
            $gene_exp = $params['magma_exp'];
            if ($gene_exp == "NA") {
                $gene_exp = "GTEx/v8/gtex_v8_ts_avg_log2TPM:GTEx/v8/gtex_v8_ts_general_avg_log2TPM";
            }
            $MHC = $params['exMHC'];
            $bkgval = $params['genetype'];
            $adjPmeth = "fdr_bh";
            $adjPcut = 0.05;
            $minOverlap = 2;

            $gval = null;
            $f = fopen(Storage::path($s2gfiledir . 'genes.txt'), 'r');
            fgetcsv($f, 0, "\t");
            while ($row = fgetcsv($f, 0, "\t")) {
                if ($gval == null) {
                    $gval = $row[0];
                } else {
                    $gval = $gval . ":" . $row[0];
                }
            }

            $paramfile = $filedir . 'params.config';
            Storage::put($paramfile, "[jobinfo]");
            Storage::append($paramfile, "created_at=$date");
            Storage::append($paramfile, "title=$title");
            Storage::append($paramfile, "snp2geneID=$s2gID");
            Storage::append($paramfile, "snp2geneTitle=$s2gTitle");
            Storage::append($paramfile, "\n[params]");
            Storage::append($paramfile, "gtype=$gtype");
            Storage::append($paramfile, "gval=$gval");
            Storage::append($paramfile, "bkgtype=$bkgtype");
            Storage::append($paramfile, "bkgval=$bkgval");
            Storage::append($paramfile, "MHC=$MHC");
            Storage::append($paramfile, "ensembl=$ensembl");
            Storage::append($paramfile, "gsFileN=0");
            Storage::append($paramfile, "gsFiles=NA");
            Storage::append($paramfile, "gene_exp=$gene_exp");
            Storage::append($paramfile, "adjPmeth=$adjPmeth");
            Storage::append($paramfile, "adjPcut=$adjPcut");
            Storage::append($paramfile, "minOverlap=$minOverlap");

            $queue = 'default';
            $user = Auth::user();
            if ($user->can('Access Priority Queue')) {
                $queue = 'high';
            }
            
                    // Queue job
            (new SubmitJob)->updateStatus($jobID, 'QUEUED');
            Gene2FuncJob::dispatch($jobID)
                ->onQueue($queue)
                ->afterCommit();

            $data = [
                'id' => $jobID,
                'filedir' => $filedir,
                'gtype' => $gtype,
                'gval' => $gval,
                'bkgtype' => $bkgtype,
                'bkgval' => $bkgval,
                'ensembl' => $ensembl,
                'gene_exp' => $gene_exp,
                'MHC' => $MHC,
                'adjPmeth' => $adjPmeth,
                'adjPcut' => $adjPcut,
                'minOverlap' => $minOverlap
            ];

            return view('pages.gene2func', ['status' => 'query', 'id' => $jobID, 'page' => 'queryhistory', 'prefix' => 'gene2func', 'data' => $data]);
        } else {
            $jobID = $checkExists->jobID;
            return redirect("gene2func/" . $jobID);
        }
    }

    public function ExpTsPlot($type, $jobID)
    {
        $filedir = config('app.jobdir') . '/gene2func/' . $jobID . '/';
        $file = "";
        if ($type == "general") {
            $file = $filedir . "ExpTsGeneral.txt";
        } else {
            $file = $filedir . "ExpTs.txt";
        }
        if (file_exists($file)) {
            $f = fopen($file, 'r');
            fgetcsv($f, 0, "\t");
            $data = [];
            $p = [];
            while ($row = fgetcsv($f, 0, "\t")) {
                $p[$row[0]] = $row[3];
                $data[] = [$row[0], $row[3], $row[4]];
            }
            asort($p);
            $order_p = [];
            $i = 0;
            foreach ($p as $key => $value) {
                $order_p[$key] = $i;
                $i++;
            }
            ksort($p);
            $order_alph = [];
            $i = 0;
            foreach ($p as $key => $value) {
                $order_alph[$key] = $i;
                $i++;
            }
            $r = ["data" => $data, "order" => ["p" => $order_p, "alph" => $order_alph]];
            return json_encode($r);
        } else {
            return;
        }
    }
}
