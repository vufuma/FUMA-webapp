<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use App\Models\SubmitJob;
use Auth;

class Helper
{
    public static function scripts_path($path = '')
    {
        return app()->basePath() . DIRECTORY_SEPARATOR . 'scripts' . ($path ? DIRECTORY_SEPARATOR . $path : $path);
    }

    public static function getFilesContents($filedir, $fileNames)
    {
        $result = array();

        foreach ($fileNames as $fileName) {
            $inputString = Storage::get($filedir . $fileName);

            // Trim any empty lines at the beginning or end of the input string.
            $inputString = trim($inputString, "\n");

            // Split the input string into an array of records using the newline character ("\n") as the delimiter.
            $records = explode("\n", $inputString);

            if (empty($records)) {
                // Handle case where no records were found
                continue;
            }

            // Split the first record into an array of headers.
            $headers = explode("\t", $records[0]);

            if (empty($headers)) {
                // Handle case where no headers were found
                // ...
            }

            // Iterate over the rest of the records and split them into arrays using the same delimiter.
            $data = array();
            for ($i = 1; $i < count($records); $i++) {
                $recordValues = explode("\t", $records[$i]);
                $record = array();
                for ($j = 0; $j < count($headers); $j++) {
                    $record[$headers[$j]] = $recordValues[$j];
                }
                $data[] = $record;
            }

            $result[$fileName] = $data;
        }
        return $result;
    }

    /**
     * This is a private function called "my_glob" that takes two parameters: $filedir (string) and $pattern (string).
     * It lists all the filenames in the given directory using the "Storage::files" method from the Laravel framework.
     * It then filters the resulting array of filenames to only include those that match a specific pattern using the "preg_grep" function.
     * The pattern is specified as a regular expression, so it can match filenames with a specific format, such as "filename.extension".
     * Finally, the function returns an array of the filenames that match the pattern.
     */
    public static function my_glob($filedir, $pattern)
    {
        // list all filenames in given path
        $allFiles = Storage::files($filedir);

        // filter the ones that match the filename.* 
        $matchingFiles = preg_grep($pattern, $allFiles);
        $matchingFiles = array_values($matchingFiles);
        // return the matching filenames
        return $matchingFiles;
    }

    public static function writeToCsv($filePath, $data, $delim = "\t")
    {
        // Open file for writing
        $file = fopen($filePath, 'w');

        // Write header
        fputcsv($file, array_keys(reset($data)), $delim);

        // Write data
        foreach ($data as $row) {
            fputcsv($file, $row, $delim);
        }

        // Close file
        fclose($file);
    }

    public static function deleteJobByAdmin($filedir, $jobID)
    {
        $job = SubmitJob::find($jobID);

        // Verify this job exists
        if (!$job) {
            return "Job not found in DB.";
        }

        // check if job is running or queued
        if (in_array($job->status, ['RUNNING', 'QUEUED'])) {
            return "Running or queued job.";
        }

        DB::beginTransaction();

        // set removed_at and removed_by fields
        $job->removed_at = date('Y-m-d H:i:s');
        $job->save();

        // check if directory is missing
        if (Storage::directoryMissing($filedir . $jobID)) {
            DB::rollBack();
            return "Directory not found.";
        }

        try {
            // delete job files
            Storage::deleteDirectory($filedir . $jobID);
        } catch (\Exception $e) {
            DB::rollBack();
            return "Failed to delete job files.";
        }

        // check if deleteDirectory failed
        if (Storage::directoryExists($filedir . $jobID)) {
            DB::rollBack();
            return "Directory was still found after deletion.";
        }
        DB::commit();
    }

    /**
     * This is a public function called "deleteJob" that takes two parameters: $filedir (string) and $jobID (integer).
     * It finds the job with the given ID using the "find" method.
     * It then checks if the job exists. If it doesn't, it returns an error message.
     * It then checks if the job belongs to the current user. If it doesn't, it returns an error message.
     * It then checks if the job is running or queued. If it is, it returns an error message.
     * If none of the above conditions are met, it starts a database transaction.
     * It sets the "removed_at" and "removed_by" fields of the job to the current date and time and the ID of the current user, respectively.
     * It then saves the job and deletes the directory containing the job files using the "Storage::deleteDirectory" method.
     * If the deletion of the directory fails, it rolls back the database transaction and returns an error message.
     * If the deletion of the directory succeeds, it commits the database transaction and returns a success message.
     */
    public static function deleteJob($filedir, $jobID)
    {
        $job = SubmitJob::find($jobID);

        // Verify this job exists
        if (!$job) {
            return "Job not found.";
        }

        // Verify this job belongs to the user
        if ($job->user_id != Auth::user()->id && !Auth::user()->can("Delete All Jobs")) {
            return "You can't delete other user's jobs.";
        }

        // check if job is running or queued
        if (in_array($job->status, ['RUNNING', 'QUEUED'])) {
            return "You can't delete running or queued jobs. PLease try again later.";
        }

        DB::beginTransaction();

        // set removed_at and removed_by fields
        $job->removed_at = date('Y-m-d H:i:s');
        $job->removed_by = Auth::user()->id;
        $job->save();

        // check if directory is missing
        if (Storage::directoryMissing($filedir . $jobID)) {
            DB::rollBack();
            return "Failed to delete job files. Please try again later.";
        }

        try {
            // delete job files
            Storage::deleteDirectory($filedir . $jobID);
        } catch (\Exception $e) {
            DB::rollBack();
            return "Failed to delete job files. Please try again later.";
        }

        // check if deleteDirectory failed
        if (Storage::directoryExists($filedir . $jobID)) {
            DB::rollBack();
            return "Failed to delete job files. Please try again later.";
        }
        DB::commit();
    }

    public static function deleteJobDirectoryOnly($filedir, $jobID)
    {
        $result = [
            'deletion_status' => False,
            'message' => 'Deleted successfully.'
        ];

        // check if directory is missing
        if (Storage::directoryMissing($filedir . $jobID)) {
            $result['message'] = "Job dir not found.";
            return $result;
        }

        try {
            // delete job files
            Storage::deleteDirectory($filedir . $jobID);
        } catch (\Exception $e) {
            $result['message'] = "Failed to delete job dir.";
            return $result;
        }

        // check if deleteDirectory failed
        if (Storage::directoryExists($filedir . $jobID)) {
            $result['message'] = "Failed to delete job dir.";
            return $result;
        }

        $result['deletion_status'] = True;
        return $result;
    }

    public static function deleteJobDbEntryOnly($jobID)
    {
        $result = [
            'deletion_status' => False,
            'message' => 'Deleted successfully.'
        ];

        $job = SubmitJob::find($jobID);

        // Verify this job exists
        if (!$job) {
            $result['message'] = "Job not found.";
            return $result;
        }

        // check if job is running or queued
        if (in_array($job->status, ['NEW', 'QUEUED', 'RUNNING'])) {
            $result['message'] = "New, queued or running jobs can't be deleted.";
            return $result;
        }

        if ($job->removed_at !== NULL) {
            $result['message'] = "Job already deleted.";
            return $result;
        }

        DB::beginTransaction();

        // set removed_at fields
        // removed_by field is not set because the job is deleted by the system
        // search for removed_at (not null) and removed_by (null) to find jobs deleted by the system
        $job->removed_at = date('Y-m-d H:i:s');
        $job->save();

        DB::commit();

        $result['deletion_status'] = True;
        return $result;
    }

    /**
     * Turn an array like the following:
     * 
     * $inputArray = [
     * 0 => [
     *    "jobID" => 444531,
     *    "type" => "snp2gene",
     *    "status" => "OK",
     *    "otherField" => "value1",
     *    // ... other fields
     *  ],
     *  1 => [
     *    "jobID" => 444530,
     *    "type" => "snp2gene",
     *    "status" => "OK",
     *    "otherField" => "value2",
     *    // ... other fields
     *  ]
     * ];
     * 
     * Into an array like the following:
     * $outputArray = [
     * 444531 => [
     *    "jobID" => 444531, (if $include_key is true)
     *    "type" => "snp2gene",
     *    "status" => "OK",
     *    "otherField" => "value1",
     *    // ... other fields
     *  ],
     *  444530 => [
     *    "jobID" => 444530, (if $include_key is true)
     *    "type" => "snp2gene",
     *    "status" => "OK",
     *    "otherField" => "value2",
     *    // ... other fields
     *  ]
     * ];
     */
    public static function flattenArrayByNestedKey($inputArray, $key_column, $include_key = False)
    {
        // It is necessary to write the outer loop twice for speed. In the first loop,
        // there is no need to check if the key exists in the output array and if the
        // include_key is true or false every time.
        $outputArray = [];
        if ($include_key) {
            foreach ($inputArray as $item) {
                $outputArray[$item[$key_column]] = $item;
            }
            return $outputArray;
        }

        foreach ($inputArray as $item) {
            $outputArray[$item[$key_column]] = [];
            foreach ($item as $key => $value) {
                if ($key !== $key_column) {
                    $outputArray[$item[$key_column]][$key] = $value;
                }
            }
        }
        return $outputArray;
    }


    public static function searchArrayByKeyValue($array, $key, $value)
    {
        foreach ($array as $item) {
            if (isset($item[$key]) && $item[$key] == $value) {
                return $item;
            }
        }
        return null;
    }
}
