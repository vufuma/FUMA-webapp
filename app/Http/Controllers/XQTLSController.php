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

    public function xqtls_sumTable(Request $request)
    {
        $id = $request->input('jobID');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        // return Storage::exists($filedir . "xqtls_results.csv");

        return myFile::summary_table_in_json($filedir . "xqtls_results.csv");
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
}