<?php

namespace App\CustomClasses\DockerApi;

use Illuminate\Support\Str;

class DockerNamesBuilder
{
    public static function imageName(string $stackName, string $imageName, string $delimiter = '-'): string
    {
        return $stackName . $delimiter . $imageName;
    }

    public static function containerName($id, string $prefix = 'job', string $sufix = '', string $delimiter = '-'): string
    {
        $uuid = Str::uuid();
        return $prefix . $delimiter . $id . $delimiter . $uuid . ($sufix === '' ? '' : $delimiter . $sufix);
    }

    public static function jobLocation($id, $jobType): string
    {
        switch ($jobType) {
            case 'snp2gene':
                $path = config('app.abs_path_to_jobs_on_host') . '/' . $id;
                break;
            case 'gene2func':
                $path = config('app.abs_path_to_g2f_jobs_on_host') . '/' . $id;
                break;
            case 'cellType':
                $path = config('app.abs_path_to_cell_jobs_on_host') . '/' . $id;
                break;
            case 'xqtls':
                $path = config('app.abs_path_to_xqtls_jobs_on_host') . '/' . $id;
                break;
        }

        return $path;
    }
}
