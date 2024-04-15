<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\SubmitJob;

class EnsureJobIsPublic
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $job = (new SubmitJob)->get_job_from_old_or_new_id_prioritizing_public($request->jobID);

        if (!is_null($job)) {
            if ($job->is_public == 1 || (!is_null($job->parent) && $job->type == 'gene2func' && $job->parent->is_public)) {
                if (in_array($job->status, ['QUEUED', 'RUNNING'])) {
                    return abort(403, "This job is still running or queued. Please wait until it's finished.");
                }
                
                return $next($request);
            }
        }

        return abort(403, "You are not authorized to access this job.");
    }
}
