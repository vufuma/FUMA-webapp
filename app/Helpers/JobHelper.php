<?php

namespace App\Helpers;

use Illuminate\Support\Facades\App;
use App\Models\SubmitJob;
use App\Mail\JobCompletedSuccessfully;
use App\Mail\JobFailedWithErrorCode;
use Mail;

use App\CustomClasses\DockerApi\DockerFactory;
use Illuminate\Support\Facades\Storage;

class JobHelper
{
    public static function sendJobMail($job, $mailer)
    {
        if (App::isProduction()) {
            try {
                Mail::to($job->user->email, $job->user->name)
                    ->send($mailer);
                return true;
            } catch (Throwable $e) {
                return false;
            }
        }
        return;
    }

    public static function JobTerminationHandling($jobID, $report_code, $msg = null)
    {
        $job = SubmitJob::find($jobID);

        $job->status = config('snp2gene_status_codes.' . $report_code . '.short_name');
        $job->completed_at = date("Y-m-d H:i:s");
        $job->save();

        if ($msg == null) {
            $msg = config('snp2gene_status_codes.' . $report_code . '.email_message');
        }

        if (config('snp2gene_status_codes.' . $report_code . '.type') == 'err') {
            // error occured
            JobHelper::sendJobMail($job, new JobFailedWithErrorCode($job, $msg));
        } elseif (config('snp2gene_status_codes.' . $report_code . '.type') == 'success') {
            // success
            JobHelper::sendJobMail($job, new JobCompletedSuccessfully($job, $msg));
        }

        if (App::isProduction()) {
            JobHelper::rmFiles($job);
        }
        return;
    }

    public static function rmFiles($job)
    {
        if ($job->type == 'snp2gene' || $job->type == 'geneMap') {
            $job_dir = config('app.jobdir') . '/jobs/' . $job->jobID;

            if (Storage::exists($job_dir . '/input.gwas')) {
                Storage::delete($job_dir . '/input.gwas');
            }

            if (Storage::exists($job_dir . '/input.snps')) {
                Storage::delete($job_dir . '/input.snps');
            }

            if (Storage::exists($job_dir . '/input.lead')) {
                Storage::delete($job_dir . '/input.lead');
            }

            if (Storage::exists($job_dir . '/input.regions')) {
                Storage::delete($job_dir . '/input.regions');
            }

            if (Storage::exists($job_dir . '/magma.input')) {
                Storage::delete($job_dir . '/magma.input');
            }
        }
        return;
    }

    public static function kill_docker_containers_based_on_jobID($jobID)
    {
        $client = new DockerFactory();
        $parameters = array(
            'label' => array(
                'com.docker.compose.project=laradock-fuma',
            ),
            'name' => array(
                'job-' . $jobID . '-',
            ),
        );
        $parameters = 'filters=' . json_encode($parameters);
        $dockerContainers = $client->dispatchCommand('/var/run/docker.sock', '/containers/json', 'GET', $parameters);

        $containers = array();
        foreach ($dockerContainers as $container) {
            array_push($containers, array(
                'name' => implode(', ', $container['Names']),
                'status' => $container['Status'],
                'service_name' => $container['Labels']['com.docker.compose.service'],
                'state' => $container['State'],
            ));
        }

        foreach ($containers as $container) {
            $client = new DockerFactory();

            $container_name = substr($container['name'], 1); // remove the first character of the container name which is '/'

            $dockerContainerRequest = $client->kill($container_name); //kill the container

            if ($dockerContainerRequest->getCurlError()) {
                // there was an error with the curl request
                // print the curl error message and curl error code 
                $err = 'Curl Error: ' . $dockerContainerRequest->getCurlError() . '   Http Response Code: ' . $dockerContainerRequest->getHttpResponseCode();
                echo $err . "\n";
                return false;
            }
            if ($dockerContainerRequest->getHttpResponseCode() == "204") {
                // the container was deleted successfully
                // there in no message from docker api
                // print the http response code $dockerContainerRequest->getHttpResponseCode()
                echo "The container: " . $container_name . " has been killed successfully.\n";
                return true;
            } else {
                // there is a message from the docker api
                // print the message $dockerContainerRequest->getMessage()
                // print the http response code $dockerContainerRequest->getHttpResponseCode()
                $err = 'Message: ' . $dockerContainerRequest->getMessage() . '   Http Response Code: ' . $dockerContainerRequest->getHttpResponseCode();
                echo $err . "\n";
                return false;
            }
        }
        return true;
    }
}
