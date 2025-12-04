<?php

namespace App\Http\Controllers;
use RuntimeException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\CustomClasses\DockerApi\DockerNamesBuilder;
use App\CustomClasses\myFile;
use App\Models\SubmitJob;
use App\Models\User;
use Jcupitt\Vips\Image as VipsImage;
use Jcupitt\Vips\Target as VipsTarget;
use PDF;

use Illuminate\Support\Facades\Log;
use App\Models\Update;

use Helper;

class FumaController extends Controller
{
    public function appinfo()
    {
        $out["ver"] = Update::orderByDesc('id')->first()->version; #sort the updates table by id and get the highest id to mean the latest update
        $out["user"] = User::get()->count();

        $out["s2g"] = SubmitJob::where('type', 'snp2gene')->get()->count();
        $out["g2f"] = SubmitJob::where('type', 'gene2func')->get()->count();
        $out["cellType"] = SubmitJob::where('type', 'celltype')->get()->count();


        $out["run"] = SubmitJob::where('status', 'RUNNING')->get()->count();
        $out["que"] = SubmitJob::where('status', 'QUEUED')->get()->count();

        return json_encode($out);
    }

    public function DTfile(Request $request)
    {
        $id = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $prefix = $request->input('prefix');
        $fin = $request->input('infile');
        $cols = $request->input('header');

        $file_path = config('app.jobdir') . '/jobs/' . $id . '/' . $fin;

        return myFile::processCsvDataWithHeaders($file_path, $cols);
    }

    public function DTfileServerSide(Request $request)
    {
        $jobID = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));

        $order =  $request->input('order');
        $search =  $request->input('search');

        $fin = escapeshellarg(escapeshellcmd($request->input('infile')));
        $cols =  escapeshellarg(escapeshellcmd($request->input('header')));
        $draw =  escapeshellarg(escapeshellcmd($request->input('draw')));
        $order_column =  escapeshellarg(escapeshellcmd($order[0]["column"]));
        $order_dir =  escapeshellarg(escapeshellcmd($order[0]["dir"]));
        $start =  escapeshellarg(escapeshellcmd($request->input('start')));
        $length =  escapeshellarg(escapeshellcmd($request->input('length')));
        $search =  escapeshellarg(escapeshellcmd($search['value']));

        $container_name = escapeshellarg(DockerNamesBuilder::containerName($jobID));
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'dt');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'snp2gene');

        $python_command = "python dt.py $job_location/ $fin $draw $cols $order_column $order_dir $start $length $search";
        $cmd = 'docker run --rm --net=none --name ' . $container_name . ' -v ' . config('app.abs_path_to_jobs_dir_on_host') . ':' . config('app.abs_path_to_jobs_dir_on_host') . ' -w /app ' . $image_name . ' /bin/sh -c "' . $python_command . '"';
        // $cmd = "docker run --rm --net=none --name " . $container_name . " -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " -w /app " . $image_name . " /bin/sh -c 'python dt.py $job_location/ $fin $draw $cols $order_column $order_dir $start $length $search'";
        $out = shell_exec($cmd);
        echo $out;

        // TODO: The following code (result of chatgpt) with some modifications could be used as a drop in replacement
        // for the dt.py code and thus to replace the docker container.
        // How to call:
        // $filedir = Storage::path(config('app.jobdir') . '/jobs/' . $jobID . '/');
        // return myFile::dt($filedir, $fin, $draw, $cols, $order_column, $order_dir, $start, $length, $search);
    }

    public function paramTable(Request $request)
    {
        $id = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $prefix = $request->input('prefix');
        $file_path = config('app.jobdir') . '/' . $prefix . '/' . $id . '/' . 'params.config';

        return myFile::parse_ini_file($file_path);
    }

    public function sumTable(Request $request)
    {
        $id = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $prefix = $request->input('prefix');
        $file_path = config('app.jobdir') . '/' . $prefix . '/' . $id . '/' . 'summary.txt';

        return myFile::summary_table_in_html($file_path);
    }

    public function locusPlot(Request $request)
    {
        $jobID = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $type = escapeshellarg(escapeshellcmd($request->input('type')));
        $rowI = escapeshellarg(escapeshellcmd($request->input('rowI')));

        $container_name = escapeshellarg(DockerNamesBuilder::containerName($jobID));
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'locus_plot');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'snp2gene');

        $python_command = "python locusPlot.py $job_location/ $rowI $type";
        $cmd = 'docker run --rm --net=none --name ' . $container_name . ' -v ' . config('app.abs_path_to_jobs_dir_on_host') . ':' . config('app.abs_path_to_jobs_dir_on_host') . ' -w /app ' . $image_name . ' /bin/sh -c "' . $python_command . '"';
        // $cmd = "docker run --rm --net=none --name " . $container_name . " -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " -w /app " . $image_name . " /bin/sh -c 'python locusPlot.py $job_location/ $rowI $type'";
        $out = shell_exec($cmd);
        return $out;
    }

    public function annotPlot(Request $request)
    {
        $jobID = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $jobID . '/';
        $type = $request->input('annotPlotSelect');
        $rowI = $request->input('annotPlotRow');

        $GWAS = 0;
        $CADD = 0;
        $RDB = 0;
        $Chr15 = 0;
        $eqtl = 0;
        $ci = 0;
        if ($request->filled('annotPlot_GWASp')) {
            $GWAS = 1;
        }
        if ($request->filled('annotPlot_CADD')) {
            $CADD = 1;
        }
        if ($request->filled('annotPlot_RDB')) {
            $RDB = 1;
        }
        if ($request->filled('annotPlot_Chrom15')) {
            $Chr15 = 1;
            $temp = $request->input('annotPlotChr15Ts');
            $Chr15cells = [];
            foreach ($temp as $ts) {
                if ($ts != "null") {
                    $Chr15cells[] = $ts;
                }
            }
            $Chr15cells = implode(":", $Chr15cells);
        } else {
            $Chr15cells = "NA";
        }
        if ($request->filled('annotPlot_eqtl')) {
            $eqtl = 1;
        }
        if ($request->filled('annotPlot_ci')) {
            $ci = 1;
        }

        return view('pages.annotPlot', [
            'jobID' => $jobID,
            'prefix' => $prefix,
            'type' => $type,
            'rowI' => $rowI,
            'GWASplot' => $GWAS,
            'CADDplot' => $CADD,
            'RDBplot' => $RDB,
            'eqtlplot' => $eqtl,
            'ciplot' => $ci,
            'Chr15' => $Chr15,
            'Chr15cells' => $Chr15cells,
            'page' => 'snp2gene/annotPlot'
        ]);
    }

    public function annotPlotGetData(Request $request)
    {
        $jobID = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $prefix = $request->input("prefix");
        $type = escapeshellarg(escapeshellcmd($request->input("type")));
        $rowI = escapeshellarg(escapeshellcmd($request->input("rowI")));
        $GWASplot = escapeshellarg(escapeshellcmd($request->input("GWASplot")));
        $CADDplot = escapeshellarg(escapeshellcmd($request->input("CADDplot")));
        $RDBplot = escapeshellarg(escapeshellcmd($request->input("RDBplot")));
        $eqtlplot = escapeshellarg(escapeshellcmd($request->input("eqtlplot")));
        $ciplot = escapeshellarg(escapeshellcmd($request->input("ciplot")));
        $Chr15 = escapeshellarg(escapeshellcmd($request->input("Chr15")));
        $Chr15cells = escapeshellarg(escapeshellcmd($request->input("Chr15cells")));

        // $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        $container_name = escapeshellarg(DockerNamesBuilder::containerName($jobID));
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'annot_plot');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'snp2gene');

        $ref_data_path_on_host = config('app.ref_data_on_host_path');

        $python_command = "python annotPlot.py $job_location/ $type $rowI $GWASplot $CADDplot $RDBplot $eqtlplot $ciplot $Chr15 $Chr15cells";
        $cmd = 'docker run --rm --net=none --name ' . $container_name . ' -v ' . $ref_data_path_on_host . ':/data -v ' . config('app.abs_path_to_jobs_dir_on_host') . ':' . config('app.abs_path_to_jobs_dir_on_host') . ' -w /app ' . $image_name . ' /bin/sh -c "' . $python_command . '"';
        // $cmd = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " -w /app " . $image_name . " /bin/sh -c 'python annotPlot.py $job_location/ $type $rowI $GWASplot $CADDplot $RDBplot $eqtlplot $ciplot $Chr15 $Chr15cells'";

        $data = shell_exec($cmd);
        return $data;
    }

    public function annotPlotGetGenes(Request $request)
    {
        $jobID = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $prefix = $request->input("prefix");
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $jobID . '/';
        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        $ensembl = $params['ensembl'];
        
        $chrom = escapeshellarg(escapeshellcmd($request->input("chrom")));
        $eqtlplot = escapeshellarg(escapeshellcmd($request->input("eqtlplot")));
        $ciplot = escapeshellarg(escapeshellcmd($request->input("ciplot")));
        $xMin = escapeshellarg(escapeshellcmd($request->input("xMin")));
        $xMax = escapeshellarg(escapeshellcmd($request->input("xMax")));
        $eqtlgenes = escapeshellarg(escapeshellcmd($request->input("eqtlgenes")));

        $container_name = escapeshellarg(DockerNamesBuilder::containerName($jobID));
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'annot_plot');
        $job_location = DockerNamesBuilder::jobLocation($jobID, 'snp2gene');

        $ref_data_path_on_host = config('app.ref_data_on_host_path');

        $Rscript = "Rscript annotPlot.R $job_location/ $chrom $xMin $xMax $eqtlgenes $eqtlplot $ciplot $ensembl";
        $cmd = 'docker run --rm --net=none --name ' . $container_name . ' -v ' . $ref_data_path_on_host . ':/data -v ' . config('app.abs_path_to_jobs_dir_on_host') . ':' . config('app.abs_path_to_jobs_dir_on_host') . ' -w /app ' . $image_name . ' /bin/sh -c "' . $Rscript . '"';
        // $cmd = "docker run --rm --net=none --name " . $container_name . " -v $ref_data_path_on_host:/data -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " -w /app " . $image_name . " /bin/sh -c 'Rscript annotPlot.R $job_location/ $chrom $xMin $xMax $eqtlgenes $eqtlplot $ciplot $ensembl'";

        $data = shell_exec($cmd);
        $data = explode("\n", $data);
        $data = $data[count($data) - 1];
        return $data;
    }

    public function legendText(Request $request)
    {
        $fileNames = $request->input('fileNames');
        $filedir = 'public/legends/';

        $result = Helper::getFilesContents($filedir, $fileNames);

        // Convert the array to a JSON string.
        return response()->json($result);
    }

    public function circos_chr(Request $request)
    {
        $id = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $filedir = config('app.jobdir') . '/jobs/' . $id . '/circos/';

        $file_paths = Helper::my_glob($filedir, "/circos_chr.*\.png/");
        $data = array();
        foreach ($file_paths as $path) {
            $name = preg_replace('/.+\/circos_chr(\d+)\.png/', '$1', $path);
            $data[$name] = base64_encode(Storage::get($path));
        }
        return response()->json(array($data));
    }

    public function circos_image($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/circos/';
        $f = File::get($filedir . $file);
        $type = File::mimeType($filedir . $file);

        return response($f)->header("Content-Type", $type);
    }

    public function circosDown(Request $request)
    {
        $jobID = (new SubmitJob)->get_job_id_from_old_or_new_id_prioritizing_public($request->input('jobID'));
        $type = $request->input('type');
        $filedir = config('app.jobdir') . '/jobs/' . $jobID . '/circos/';
        $zip = new \ZipArchive();
        $zipfile = "job" . $jobID . "_circos_" . $type . ".zip";

        if ($type == "conf") {
            $file_paths = Helper::my_glob($filedir, "/.*\.txt/");
            foreach ($file_paths as $path) {
                $files[] = preg_replace("/.+\/(\w+\.txt)/", '$1', $path);
            }
        } else {
            $file_paths = Helper::my_glob($filedir, "/.*\." . $type . "/");
            foreach ($file_paths as $path) {
                $files[] = preg_replace("/.+\/(\w+\.$type)/", '$1', $path);
            }
        }

        $zip->open(Storage::path($filedir . $zipfile), \ZipArchive::CREATE);
        foreach ($files as $f) {
            $abs_path = Storage::path($filedir . $f);
            $zip->addFile($abs_path, $f);
        }
        $zip->close();

        return response()->download(Storage::path($filedir . $zipfile))->deleteFileAfterSend(true);
    }

    // The use of this function is deprecated.
    // Raster conversion can be donw using libvips via jcupitt/vips
    // PDF conversion using TCPDF via elibyy/tcpdf-laravel
    public function imgdown_imagick(Request $request)
    {
        $svg = $request->input('data');
        $jobID = $request->input('jobID');
        $type = $request->input('type');
        $fileName = $request->input('fileName') . "_FUMA_" . "jobs" . $jobID;

        $svg = preg_replace("/\),rotate/", ")rotate", $svg);
        $svg = preg_replace("/,skewX\(.+?\)/", "", $svg);
        $svg = preg_replace("/,scale\(.+?\)/", "", $svg);

        if ($type == "svg") {
            $filename = $fileName . '.svg';
            return response()->streamDownload(function () use ($svg) {
                echo $svg;
            }, $filename);
        } else {
            $fileName = $fileName . '.' . $type;
            $image = new \Imagick();
            $image->setResolution(300, 300);
            $image->setBackgroundColor(new \ImagickPixel('white'));
            $image->readImageBlob('<?xml version="1.0"?>' . $svg);
            $image->setImageFormat($type);
            return response()->streamDownload(function () use ($image) {
                echo $image;
            }, $fileName);
        }
    }

    // PDF conversion using TCPDF via elibyy/tcpdf-laravel
    private function svgToPdf($svg, $jobID, $abs_path)
    {
        PDF::SetCreator('FUMA + tc-lib-pdf');
        PDF::SetTitle('FUMA Job ', $jobID);
        PDF::AddPage();
        PDF::ImageSVG('@' . $svg, $x=null, $y=null, $w=0, $h=0, $link='', $align='', $palign='', $border=0, $fitonpage=true); 
        PDF::Output($abs_path);

        PDF::reset();
    }

    // Raster conversion can be donw using libvips via jcupitt/vips
    private function svgToRasterFormat($svg, $jobID, $type, $abs_path)
    {
        // Why we are not using jcupitt/vips 2.*
        // For libvips FFI preload does not work
        // because vips.h is too complex.
        // Therefore there is no easy way to address
        // the security issues of a completely open FFI.
        // Instead this is using the (JCupitt) php-vips 1.* interface and
        // vips php extension which does not rely on FFI. 
        if (!extension_loaded('vips')) {
            throw new RuntimeException("The vips extension is not loaded");
        }

        $bufferData = '<?xml version="1.0"?>' . $svg;
        // These options are passed to the loader
        // The options per image type are documents in the
        // corresponding libvips commandline tool:
        // jpeg -> jpegsave
        // png -> pngsave
        // webp -> webpsave
        // For available options refer to 
        // https://raw.githubusercontent.com/libvips/libvips/refs/tags/v8.16.0/cplusplus/include/vips/VImage8.h
        $image = VipsImage::newFromBuffer($bufferData, 'dpi=300');
        // Overlay the image onto a white background 
        // also works for png whereas targetOptions 'background' => '255'
        // does not. 
        $background = $image->newFromImage([255, 255, 255, 255]);
        $image = $background->composite($image, 'over');
        // These options are passed to the saver
        // Each image type supports different options but
        // a few are common.
        $targetOptions = [
            'Q' => 100,
        ];

        $image->writeToFile(
            $abs_path, 
            $targetOptions
        );
    }

    // This version of the imgdown uses libvips for the
    // rendering step. This correctly renders even complex
    // svg images. Imagick is still used but only for pdf
    // output.  
    public function imgdown(Request $request)
    {
        try {
            $svg = $request->input('data');
            $jobID = $request->input('jobID');
            $type = $request->input('type');
            $dir = $request->input('dir');
            $fileName = $request->input('fileName') . "_FUMA_" . $dir . $jobID;

            if ($type == "svg") {
                $filename = $fileName . '.svg';
                return response()->streamDownload(function () use ($svg) {
                    echo $svg;
                }, $filename);
            } else {
                $type;
                $filedir = config('app.jobdir') . '/' . $dir . '/' . $jobID . '/';
                $abs_path = Storage::path($filedir . $fileName . '.' . $type);

                if (strcmp($type, "pdf") == 0) {
                    $this->svgToPdf($svg, $jobID, $abs_path);
                } else {
                    $this->svgToRasterFormat($svg, $jobID, $type, $abs_path);
                }
                return response()->download($abs_path)->deleteFileAfterSend(true);
            }
        } catch (Exception $e) {
            Log::error('Error in svg image conversion FumaController::imgdown: ' . $e-getMessage());
            return abort(500, 'Internal Server Error, please contact support.');
        }

    }

    public function d3text($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        $f = $filedir . $file;
        if (file_exists($f)) {
            $file = fopen($f, 'r');
            $header = fgetcsv($file, 0, "\t");
            $all_rows = array();
            while ($row = fgetcsv($file, 0, "\t")) {
                $all_rows[] = array_combine($header, $row);
            }
            echo json_encode($all_rows);
        }
    }

    public function g2f_filedown(Request $request)
    {
        $id = $request->input('jobID');
        $id_tmp = $id;
        $prefix = $request->input('prefix');

        if ($prefix == "public") {
            $parent_job = (new SubmitJob)->get_job_from_old_or_new_id_prioritizing_public($id);
            $id = $parent_job->child->jobID;
        }

        $filedir = config('app.jobdir') . '/gene2func/' . $id . '/';

        $files = [];
        if ($request->filled('summaryfile')) {
            $files[] = "summary.txt";
        }
        if ($request->filled('paramfile')) {
            $files[] = "params.config";
        }
        if ($request->filled('geneIDfile')) {
            $files[] = "geneIDs.txt";
        }
        if ($request->filled('expfile')) {
            $tmp = Helper::my_glob($filedir, "/.*\_exp.txt/");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.*\/(.*_exp.txt)/", "$1", $tmp[$i]);
            }
        }
        if ($request->filled('DEGfile')) {
            $tmp = Helper::my_glob($filedir, "/.*\_DEG.txt/");
            for ($i = 0; $i < count($tmp); $i++) {
                $files[] = preg_replace("/.*\/(.*_DEG.txt)/", "$1", $tmp[$i]);
            }
        }
        if ($request->filled('gsfile')) {
            $files[] = "GS.txt";
        }
        if ($request->filled('gtfile')) {
            $files[] = "geneTable.txt";
        }

        if ($prefix == "public") {
            $zipfile = $filedir . "FUMA_gene2func_public" . $id_tmp . ".zip";
        } else {
            $zipfile = $filedir . "FUMA_gene2func" . $id . ".zip";
        }
        if (Storage::exists($zipfile)) {
            Storage::delete($zipfile);
        }

        # create zip file and open it
        $zip = new \ZipArchive();
        $zip->open(Storage::path($zipfile), \ZipArchive::CREATE);

        # add README file if exists in the public storage
        if (Storage::disk('public')->exists('README_g2f.txt')) {
            $zip->addFile(Storage::disk('public')->path('README_g2f.txt'), "README_g2f");
        }

        # for each file, check if exists in the storage and add to zip file
        foreach ($files as $f) {
            if (Storage::exists($filedir . $f)) {
                $abs_path = Storage::path($filedir . $f);
                $zip->addFile($abs_path, $f);
            }
        }

        # close zip file
        $zip->close();

        # download zip file and delete it after download
        return response()->download(Storage::path($zipfile))->deleteFileAfterSend(true);
    }

    public function download_variants(Request $request)
    {
        $code = $request->input('variant_code');
        # Log::error("Variant code $code");
        $path = null;
        $name = null;
        switch ($code) {
            case "ALL":
                $name = "1KGphase3ALLvariants.txt.gz";
                break;
            case "AFR":
                $name = "1KGphase3AFRvariants.txt.gz";
                break;
            case "AMR":
                $name = "1KGphase3AMRvariants.txt.gz";
                break;
            case "EAS":
                $name = "1KGphase3EASvariants.txt.gz";
                break;
            case "EUR":
                $name = "1KGphase3EURvariants.txt.gz";
                break;
            case "SAS":
                $name = "1KGphase3SASvariants.txt.gz";
                break;
            case "MSigDB7":
                $name = "MSigDB7_MAGMA.txt";
                break;
            case "MSigDB20231Hs":
                $name = "MSigDB_20231Hs_MAGMA.txt";
                break;
            case "GENE2FUNC1":
                $name = "GENE2FUNC_genesets_v135d_to_v155.tar.gz";
                break;
            case "GENE2FUNC2":
                $name = "GENE2FUNC_genesets_v156plus.tar.gz";
                break;
            case "GTExDEG30v8":
                $name = "gtex_v8_ts_general_DEG.txt";
                break;
            case "GTExDEG54v8":
                $name = "gtex_v8_ts_DEG.txt";
                break;
            case "GTExDEG30v7":
                $name = "gtex_v7_ts_general_DEG.txt";
                break;
            case "GTExDEG54v7":
                $name = "gtex_v7_ts_DEG.txt";
                break;
            case "GTExDEG30v6":
                $name = "gtex_v6_ts_general_DEG.txt";
                break;
            case "GTExDEG54v6":
                $name = "gtex_v6_ts_DEG.txt";
                break;
            case "GTEx30v8":
                $name = "gtex_v8_ts_general_avg_log2TPM.txt";
                break;
            case "GTEx54v8":
                $name = "gtex_v8_ts_avg_log2TPM.txt";
                break;
            case "GTEx30v7":
                $name = "gtex_v7_ts_general_avg_log2TPM.txt";
                break;
            case "GTEx54v7":
                $name = "gtex_v7_ts_avg_log2TPM.txt";
                break;
            case "GTEx30v6":
                $name = "gtex_v6_ts_general_avg_log2RPKM.txt";
                break;
            case "GTEx54v6":
                $name = "gtex_v6_ts_avg_log2RPKM.txt";
                break;
            case "MAGMAgenev85":
                $name = "ENSGv85.coding.genes.txt";
                break;
            case "MAGMAgenev92":
                $name = "ENSGv92.coding.genes.txt";
                break;
            case "MAGMAgenev102":
                $name = "ENSGv102.coding.genes.txt";
                break;
            case "MAGMAgenev110":
                $name = "ENSGv110.coding.genes.txt";
                break;
            case "GRCh382rsID":
                $name = "GRCh38_to_rsID_dbSNPv152.txt.gz";
                break;
            
            // MAGMA gene sets 
            case "MAGMA_EUR_bim":
                $name = "EUR.bim";
                break;
            case "MAGMA_EUR_bed":
                $name = "EUR.bed";
                break;
            case "MAGMA_EUR_fam":
                $name = "EUR.fam";
                break;

            case "MAGMA_AFR_bim":
                $name = "AFR.bim";
                break;
            case "MAGMA_AFR_bed":
                $name = "AFR.bed";
                break;
            case "MAGMA_AFR_fam":
                $name = "AFR.fam";
                break;

            case "MAGMA_AMR_bim":
                $name = "AMR.bim";
                break;
            case "MAGMA_AMR_bed":
                $name = "AMR.bed";
                break;
            case "MAGMA_AMR_fam":
                $name = "AMR.fam";
                break;

            case "MAGMA_EAS_bim":
                $name = "EAS.bim";
                break;
            case "MAGMA_EAS_bed":
                $name = "EAS.bed";
                break;
            case "MAGMA_EAS_fam":
                $name = "EAS.fam";
                break;

            case "MAGMA_SAS_bim":
                $name = "SAS.bim";
                break;
            case "MAGMA_SAS_bed":
                $name = "SAS.bed";
                break;
            case "MAGMA_SAS_fam":
                $name = "SAS.fam";
                break;

            case "MAGMA_ALL_bim":
                $name = "ALL.bim";
                break;
            case "MAGMA_ALL_bed":
                $name = "ALL.bed";
                break;
            case "MAGMA_ALL_fam":
                $name = "ALL.fam";
                break;

            // Pre-processed scRNAseq
            case "scrnaseqDatasets";
                $name = "preprocessed_scrnaseq.tar.gz";
                break;

            default:
                return redirect()->back();
        }
        // Try direct download from reference data
        $ref_data_path_on_host = config('app.ref_data_on_host_path');
        $downloadDir = config("app.downloadsDir");
        $downloadPath = $ref_data_path_on_host . '/' . $downloadDir . "/$name";
        if (!file_exists($downloadPath)) {
            // Or failing that download from storage
            $downloadPath = Storage::path(config("app.downloadsDir") . "/$name");
        }
        # Log::error("Variant path $path");
        $headers = array('Content-Type: application/gzip');
        return response()->download($downloadPath, $name, $headers);
    }

    public function g2f_d3text($prefix, $id, $file)
    {
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';
        if ($prefix == "public") {
            $filedir .= 'g2f/';
        }

        if (Storage::exists($filedir . $file)) {
            $file = Helper::getFilesContents($filedir, [$file]);
            return response()->json($file);
        }
        return response()->json([]);
    }

    public function g2f_sumTable(Request $request)
    {
        $id = $request->input('jobID');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        return myFile::summary_table_in_json($filedir . "summary.txt");
    }

    public function expDataOption(Request $request)
    {
        $id = $request->input('jobID');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        $params = parse_ini_string(Storage::get($filedir . 'params.config'), false, INI_SCANNER_RAW);
        return $params['gene_exp'];
    }

    public function expPlot($prefix, $id, $dataset)
    {
        $dataset = escapeshellarg(escapeshellcmd($dataset));

        $container_name = escapeshellarg(DockerNamesBuilder::containerName($id));
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'g2f');
        $job_location = DockerNamesBuilder::jobLocation($id, 'gene2func');

        $python_command = "python g2f_expPlot.py $job_location/ " . $dataset;
        $cmd = 'docker run --rm --net=none --name ' . $container_name . ' -v ' . config('app.abs_path_to_jobs_dir_on_host') . ':' . config('app.abs_path_to_jobs_dir_on_host') . ' ' . $image_name . ' /bin/sh -c "' . $python_command . '"';
        $data = shell_exec($cmd);
        return $data;
    }

    public function DEGPlot($prefix, $id)
    {
        $container_name = escapeshellarg(DockerNamesBuilder::containerName($id));
        $image_name = DockerNamesBuilder::imageName('laradock-fuma', 'g2f');
        $job_location = DockerNamesBuilder::jobLocation($id, 'gene2func');

        $cmd = "docker run --rm --net=none --name " . $container_name . " -v " . config('app.abs_path_to_jobs_dir_on_host') . ":" . config('app.abs_path_to_jobs_dir_on_host') . " " . $image_name . " /bin/sh -c 'python g2f_DEGPlot.py $job_location/'";
        $data = shell_exec($cmd);
        return $data;
    }

    public function geneTable(Request $request)
    {
        // TODO: make this function use column names instead of column indecies
        $id = $request->input('jobID');
        $prefix = $request->input('prefix');
        $filedir = config('app.jobdir') . '/' . $prefix . '/' . $id . '/';

        if (Storage::exists($filedir . "geneTable.txt")) {
            $f = fopen(Storage::path($filedir . "geneTable.txt"), 'r');
            $head = fgetcsv($f, 0, "\t");
            $head[] = "GeneCard";
            $all_rows = [];
            while ($row = fgetcsv($f, 0, "\t")) {
                if (strcmp($row[4], "NA") != 0) {
                    $row[4] = '<a href="https://www.omim.org/entry/' . $row[4] . '" target="_blank">' . $row[4] . '</a>';
                }
                if (strcmp($row[6], "NA") != 0) {
                    $db = explode(":", $row[6]);
                    $row[6] = "";
                    foreach ($db as $i) {
                        if (strlen($row[6]) == 0) {
                            $row[6] = '<a href="https://www.drugbank.ca/drugs/' . $i . '" target="_blank">' . $i . '</a>';
                        } else {
                            $row[6] .= ', <a href="https://www.drugbank.ca/drugs/' . $i . '" target="_blank">' . $i . '</a>';
                        }
                    }
                }
                $row[] = '<a href="http://www.genecards.org/cgi-bin/carddisp.pl?gene=' . $row[2] . '" target="_blank">GeneCard</a>';
                $all_rows[] = array_combine($head, $row);
            }

            $json = array('data' => $all_rows);
            return json_encode($json);
        } else {
            return '{"data": []}';
        }
    }
}
