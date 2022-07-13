<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Http\Request;
use Auth;
use fuma\Jobs\snp2geneProcess;
use fuma\SubmitJob;

use fuma\User;

/**
 * Test SNP2GENE jobs. 
 * This runs a low level confidence test that runs through
 * the steps in SNP2GENE using artifically created GWAS
 * data.
 * @package 
 */
class S2GTest extends TestCase
{
    public static $testName = 'TestS2GJob';
    public static $testUser = array(
        "email" => "admin@test.com",
        "name" => "Test Admin"
    );
    // Max int for 64bit PHP is max ID for sqlite3
    public static $testID = PHP_INT_MAX;
    public function setUp()
    {
        parent::setUp();
        Gate::before(function () {
            return true;
        });
        $admin = User::where('name', "Test Admin")->first();
        if ($admin != null) {
            $admin->delete();
        }
        $this->actingAsAdmin();
		// Create new test in database immediately in the QUEUED state
        // with the special test ID.
		$uniqueTestName = uniqid(S2GTest::$testName . '_');
        $date = date('Y-m-d H:i:s');
        // cleanup any previous tests
        DB::table('SubmitJobs')->where('jobID', S2GTest::$testID)->delete();
        DB::table('JobMonitor')->where('jobID', S2GTest::$testID)->delete();
        DB::table('SubmitJobs')->insert(
            [
                'jobID' => S2GTest::$testID,
                'email' => S2GTest::$testUser['email'],
                'title' => $uniqueTestName,
                'created_at' => $date,
                'updated_at' => $date,
                'status' => 'QUEUED'
            ]
            );
        $testJob = DB::table('SubmitJobs')->where('jobID', S2GTest::$testID)->first();
        $this->assertNotNull($testJob);
    }

    function testSmall()
    {
        // Check test job setup

        $testJob = DB::table('SubmitJobs')->where('jobID', S2GTest::$testID)->first();
        $this->assertNotNull($testJob);
        print("\n Testing jobID: ".$testJob->jobID."\n");

        $testUser = Auth::user();

        $testAssetRoot = config('app.testAssetRoot');
        $smallTestRoot = $testAssetRoot.'/S2G/small_test/';
        $smallTestIn = $smallTestRoot.'in';
        $smallTestOut = $smallTestRoot.'out';
        $this->assertEquals(PHP_INT_MAX, S2GTest::$testID);
        // copy the input data to the job root
        print("\nApp env: ".config('app.env')."\n");
        $jobDir = config('app.jobdir').'/jobs/'.S2GTest::$testID;
        print("\n");
        print("job dir: ".$jobDir." test source dir: ".$smallTestIn);
        print("\n");
        if (File::exists($jobDir)) {
            File::deleteDirectory($jobDir);
        }
        $this->assertTrue(File::copyDirectory($smallTestIn, $jobDir));
        $this->assertTrue(File::exists($jobDir));
        $files = File::allFiles($jobDir);
        print("\nTest contents:");  
        foreach ($files as $file) {
            print("\n".$file);
        }

        // Create and run job
        $job = new snp2geneProcess($testUser, S2GTest::$testID);
        $job->handle();

        // Check after job completion
        $testJob = DB::table('SubmitJobs')->where('jobID', S2GTest::$testID)->first();
        $this->assertNotNull($testJob);

        // Check job status
        print("\n");
        print("Finished job status: ".$testJob->status."\n");
        print("\n");
        $this->assertEquals("OK", $testJob->status);

        // Check job output - files in order of last modification
        // These outputs are too variable for a simple diff:
        //             'manhattan.txt',
        //             'QQSNPs.txt',
        //             'annov.exonic_variant_function',
        //             'annov.txt'
        $files_to_check = [
            'magma.genes.raw',
            'magma.gsa.out',
            'magma_exp_gtex_v8_ts_avg_log2TPM.gsa.out',
            'magma_exp_gtex_v8_ts_general_avg_log2TPM.gsa.out',
            'magma.genes.out',
            'magma.sets.top',
            'ld.txt',
            'annot.txt',
            'leadSNPs.txt',
            'GenomicRiskLoci.txt',
            'annov.input',
            'annov.variant_function',
            'IndSigSNPs.txt',
            'annov.stats.txt',
            'gwascatalog.txt',
            'ciSNPs.txt',
            'ciProm.txt',
            'eqtl.txt',
            'ci.txt',
            'snps.txt',
            'genes.txt',
            'summary.txt',
            'interval_sum.txt'
        ];
        foreach($files_to_check as $file_name) {
            print("\nChecking output: ".$file_name);
            $this->assertFileEquals($smallTestOut.'/'.$file_name, $jobDir.'/'.$file_name, 'Failed: '.$file_name);
        }
        
    }

    function actingAsAdmin()
    {
        $this->actingAs(factory(fuma\User::class)->create(S2GTest::$testUser));
    }

    public function tearDown() {
        User::where('name', "Test Admin")->first()->delete();
        parent::tearDown();
        // TODO Decide if also cleanup test jobs?
      }
 }