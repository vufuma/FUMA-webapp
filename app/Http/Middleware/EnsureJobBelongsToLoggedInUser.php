<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use App\Models\SubmitJob;
use Auth;

class EnsureJobBelongsToLoggedInUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $job = SubmitJob::find($request->jobID);

        if ($job != null && ($job->user->id == Auth::user()->id || Auth::user()->can('Load Private Jobs') || Auth::user()->can('Delete All Jobs'))) {
            if (in_array($job->status, ['QUEUED', 'RUNNING'])) {
                return abort(403, "This job is still running or queued. Please wait until it's finished.");
            }

            return $next($request);
        }

        return abort(403, "You are not authorized to access this job.");
    }
}
