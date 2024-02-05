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

        if ($job != null && $job->user->id == Auth::user()->id) {
            return $next($request);
        }

        return abort(403, "You are not authorized to access this job.");
    }
}
