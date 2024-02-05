<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

use App\Models\SubmitJob;

use File;

class BrowseController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index($id = null)
    {
        if (!is_null($id) && !(new SubmitJob)->find_public_job_from_id($id)) {
            return redirect()->route('login');
        }

        return view('pages.browse', ['id' => $id, 'page' => 'browse', 'prefix' => 'public']);
    }

    public function getGwasList()
    {
        $results = SubmitJob::where('is_public', 1)
            ->whereNull('removed_at')
            ->orderBy('published_at', 'desc')
            ->get([
                'jobID',
                'old_id',
                'title',
                'author',
                'publication_email',
                'phenotype',
                'publication',
                'sumstats_link',
                'sumstats_ref',
                'notes',
                'published_at'
            ])
            ->toArray();

        foreach ($results as &$result) {
            foreach ($result as &$field) {
                $field = (is_null($field) ? '' : $field);
            }
        }

        return response()->json($results);
    }

    public function checkG2F(Request $request)
    {
        $old_id = $request->input('jobID');

        $public_job = (new SubmitJob)->find_public_job_from_id($old_id);
        $public_job_gene2func_child = $public_job->childs
            ->where('type', 'gene2func')
            ->whereNull('removed_at')
            ->first();

        if (is_null($public_job_gene2func_child)) {
            return response()->json(['status' => 'error', 'message' => 'No G2F job found.']);
        }

        return $public_job_gene2func_child->jobID;
    }

    public function getParams(Request $request)
    {
        $jobID = (new SubmitJob)->get_public_job_id_from_old_or_not_id($request->input('jobID'));
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/';
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        $posMap = $params['posMap'];
        $eqtlMap = $params['eqtlMap'];
        $orcol = $params['orcol'];
        $becol = $params['becol'];
        $secol = $params['secol'];
        $ciMap = 0;
        if (array_key_exists('ciMap', $params)) {
            $ciMap = $params['ciMap'];
        }
        $magma = $params['magma'];
        return "$posMap:$eqtlMap:$ciMap:$orcol:$becol:$secol:$magma";
    }
}
