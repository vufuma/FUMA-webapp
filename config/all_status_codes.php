<?php

return [
    0 => [
        'short_name' => 'ERROR:0',
        'long_name' => 'Error code 0',
        'description' => 'input.gwas file is missing',
        'email_message' => '
                            <p>File upload failed.</p>
                            <p>This error is because your file upload failed. Please try again. Do not leave the page while uploading 
                            the file (after clicking the submit button).</p>
                            <ol>
                                <li>Only click the "Submit Job" once.</li>
                                <li>Try using a different browser and/or hard refresh the page. (Ctrl + shift + r)</li>
                                <li>Try using VPN.</li>
                                <li>Try a personal computer from a home network. (Sometimes networks in working environments are very strict)</li>
                            </ol>
                            ',
        'type' => 'err',
    ],

    1 => [
        'short_name' => 'ERROR:100',
        'long_name' => 'Error code 100',
        'description' => 'params.config file is missing',
        'email_message' => '<p>Job parameters input file is missing, internal server error.</p>',
        'type' => 'err',
    ],

    2 => [
        'short_name' => 'ERROR:001',
        'long_name' => 'Error code 001',
        'description' => 'gwas_file.py failed',
        'email_message' => '<p>This error means that the input file format was not correct.</p>',
        'type' => 'err',
    ],

    3 => [
        'short_name' => 'ERROR:002',
        'long_name' => 'Error code 002',
        'description' => 'allSNPs.py failed',
        'email_message' => '',
        'type' => 'err',
    ],

    4 => [
        'short_name' => 'ERROR:magma',
        'long_name' => 'Error code magma',
        'description' => 'magma.py failed',
        'email_message' => '
                            <p>This error can occur if the rsID and/or p-value columns are mistakenly labelled wrong.</p>
                            <ol>
                                <li>If magma was not able to perform, make sure that you selected this option during submission</li>
                                <li>Search <a href="https://groups.google.com/g/fuma-gwas-users">FUMA GWAS users - Google Groups</a> for magma to 
                                see if your issues have been asked and answered before</li>
                            </ol>
                            ',
        'type' => 'err',
    ],

    5 => [
        'short_name' => 'ERROR:003',
        'long_name' => 'Error code 003',
        'description' => 'manhattan_filt.py failed',
        'email_message' => '
                            <p>Error during SNPs filtering for Manhattan plot. 
                            This error can occur if the p-value column is mistakenly labelled wrong.</p>
                            ',
        'type' => 'err',
    ],

    6 => [
        'short_name' => 'ERROR:004',
        'long_name' => 'Error code 004',
        'description' => 'QQSNPs_filt.py failed',
        'email_message' => '
                            <p>Error during SNPs filtering for Manhattan plot. 
                            This error can occur if the p-value column is mistakenly labelled wrong.</p>
                            ',
        'type' => 'err',
    ],

    7 => [
        'short_name' => 'ERROR:005',
        'long_name' => 'Error code 005',
        'description' => 'NoCandidates',
        'email_message' => '
                            <p>Error from lead SNPs and candidate SNPs identification / No significant SNPs were identified.</p>
                            <p>This error can occur when no candidate SNPs are identified. Note that indels are included in the FUMA from 
                            v1.3.0 but both alleles need to match exactly with the selected reference panel.</p>
                            <p>MHC region is also excluded by default.</p>
                            <ul>
                                <li>If there is no significant hit at your defined P-value cutoff for lead SNPs and GWAS-tagged SNPs, you 
                                can try to use a less stringent P-value threshold or provide predefined lead SNPs.</li>
                                <li>If there are significant SNPs with very low minor allele frequency, try decreasing MAF threshold (default 0.01).</li>
                            </ul>
                            <p>Manhattan plots and significant-top 10 SNPs in your input file are available from SNP2GENE.</p>
                            <p>You will also get this error if the chr:pos:rsID combination does not match with the reference panel 
                            (which is in hg19 format) (see previous thread: <a href="https://groups.google.com/g/fuma-gwas-users/c/coB3sLR6wUM/m/SvP84zpMAAAJ">Error:005, 3 jobs</a>)</p>
                            ',
        'type' => 'err',
    ],

    8 => [
        'short_name' => 'ERROR:006',
        'long_name' => 'Error code 006',
        'description' => 'Candidates found',
        'email_message' => '
                            <p>Error from lead SNPs and candidate SNPs identification. This error can occur because:</p>
                            <ol>
                                <li>Invalid input parameters or</li>
                                <li>Columns are mistakenly labelled wrong.</li>
                            </ol>
                            <p>Please make sure your input file has the correct column names.</p>
                            <p>Please refer to the tutorial for details.</p>
                            ',
        'type' => 'err',
    ],

    9 => [
        'short_name' => 'ERROR:007',
        'long_name' => 'Error code 007',
        'description' => 'SNPannot.R failed',
        'email_message' => '
                            <p>Error during SNP annotation extraction. This error can occur because:</p>
                            <ol>
                                <li>Invalid input parameters or</li>
                                <li>Columns are mistakenly labelled wrong.</li>
                            </ol>
                            <p>Please make sure your input file has the correct column names.</p>
                            <p>Please refer to the tutorial for details.</p>
                            ',
        'type' => 'err',
    ],

    10 => [
        'short_name' => 'ERROR:008',
        'long_name' => 'Error code 008',
        'description' => 'getGWAScatalog.py failed',
        'email_message' => '
                            <p>Error during extracting external data sources. This error can occur because:</p>
                            <ol>
                                <li>Invalid input parameters or</li>
                                <li>Columns are mistakenly labelled wrong.</li>
                            </ol>
                            <p>Please make sure your input file has the correct column names.</p>
                            <p>Please refer to the tutorial for details.</p>
                            ',
        'type' => 'err',
    ],

    11 => [
        'short_name' => 'ERROR:009',
        'long_name' => 'Error code 009',
        'description' => 'geteQTL.py failed',
        'email_message' => '
                            <p>Error during extracting external data sources. This error can occur because:</p>
                            <ol>
                                <li>Invalid input parameters or</li>
                                <li>Columns are mistakenly labelled wrong.</li>
                            </ol>
                            <p>Please make sure your input file has the correct column names.</p>
                            <p>Please refer to the tutorial for details.</p>
                            ',
        'type' => 'err',
    ],

    12 => [
        'short_name' => 'ERROR:010',
        'long_name' => 'Error code 010',
        'description' => 'getCI.R failed',
        'email_message' => '
                            <p>Error from chromatin interaction mapping. This error might be because one of the uploaded chromatin 
                            interaction files did not follow the correct format.</p>
                            <p>Please refer to the tutorial for details.</p>
                            ',
        'type' => 'err',
    ],

    13 => [
        'short_name' => 'ERROR:011',
        'long_name' => 'Error code 011',
        'description' => 'geneMap.R failed',
        'email_message' => '
                            <p>Error during gene mapping. This error can occur because:</p>
                            <ol>
                                <li>Invalid input parameters or</li>
                                <li>Columns are mistakenly labelled wrong.</li>
                            </ol>
                            <p>Please make sure your input file has the correct column names.</p>
                            <p>Please refer to the tutorial for details.</p>
                            ',
        'type' => 'err',
    ],

    14 => [
        'short_name' => 'ERROR:012',
        'long_name' => 'Error code 012',
        'description' => 'createCircosPlot.py failed',
        'email_message' => '
                            <p>Error from circos. This error is most likely due to server-side error. Please contact the developer for details.</p>
                            ',
        'type' => 'err',
    ],

    15 => [
        'short_name' => 'OK',
        'long_name' => '',
        'description' => '',
        'email_message' => '',
        'type' => 'success',
    ],

    16 => [
        'short_name' => 'ERROR:Undefined',
        'long_name' => '',
        'description' => '',
        'email_message' => '
                            <p>This may be a result of job submission failure, job abort or perhaps a server-side error. 
                            If this persists please contact the developer for details.</p>
                            ',
        'type' => 'err',
    ],

    17 => [
        'short_name' => 'ERROR:timeout',
        'long_name' => '',
        'description' => '',
        'email_message' => '
                            <p>This error occurs when processing time limit (8 hours) for a specific task or operation is exceeded. 
                            It often indicates that the system was unable to complete the task within the expected timeframe, 
                            possibly due to heavy load. Users encountering this error may need to retry the operation later or contact 
                            support if the issue persists.</p>
                            ',
        'type' => 'err',
    ],

    18 => [
        'short_name' => 'ERROR:cellType',
        'long_name' => '',
        'description' => '',
        'email_message' => "
                            <p>An error occurred during the process of your cell type specificity analyses. Please make sure that your provided 
                            inputs meet all the requirements and check the following:</p>
                            <ol>
                                <li>Does your selected SNP2GENE job have MAGMA output? If you can see manhattan plot for gene-based test, 
                                this should not be the problem.</li>
                                <li>Is your uploaded file an output of MAGMA gene analysis with an extension <genes.raw>?</li>
                                <li>Does your file contain Ensembl gene ID? Otherwise, don't forget to UNCHECK the option to indicate 
                                that you are using Ensembl gene ID.</li>
                            </ol>
                            <p>If any of those don't solve the problem, please contact the developer.</p>
                            ",
        'type' => 'err',
    ],
    19 => [
        'short_name' => 'ERROR:coloc',
        'long_name' => 'Error code coloc',
        'description' => 'An error occurred during colocalization analysis',
        'email_message' => '<p>An error occurred during colocalization analysis.</p>',
        'type' => 'err',
    ],
    20 => [
        'short_name' => 'ERROR:lava',
        'long_name' => 'Error code lava',
        'description' => 'An error occurred during LAVA analysis',
        'email_message' => '<p>An error occurred during LAVA analysis.</p>',
        'type' => 'err',
    ],

    21 => [
        'short_name' => 'ERROR:colocTissueLookup',
        'long_name' => 'Error code tissue name not found in sample size lookup table',
        'description' => 'Tissue name not found in sample size lookup table',
        'email_message' => '<p>The tissue name was not found in the sample size lookup table.</p>',
        'type' => 'err',
    ],
    22 => [
        'short_name' => 'ERROR:colocNoGenesInLocus',
        'long_name' => 'Error code no genes in locus',
        'description' => 'No genes found in the locus',
        'email_message' => '<p>No genes were found in the locus.</p>',
        'type' => 'err',
    ],
    23 => [
        'short_name' => 'ERROR:inputGwasHeader',
        'long_name' => 'Error code incorrect header format for input gwas summary statistics',
        'description' => 'Incorrect header format for the input gwas summary statistics for the locus',
        'email_message' => '<p>Incorrect header format for the input gwas summary statistics for the locus.</p>',
        'type' => 'err',
    ],
    24 => [
        'short_name' => 'ERROR:inputGwasProcessing',
        'long_name' => 'Error code error occurs when processing input gwas summary statistics',
        'description' => 'An error occurs when processing the input gwas summary statistics for the locus',
        'email_message' => '<p>An error occurs when processing the input gwas summary statistics for the locus.</p>',
        'type' => 'err',
    ],
    25 => [
        'short_name' => 'ERROR:xQTLFormatting',
        'long_name' => 'Error code error occurs when formatting xQTL datasets',
        'description' => 'An error occurs when formatting the xQTL datasets for LAVA and colocalization',
        'email_message' => '<p>An error occurs when formatting the xQTL datasets for LAVA and colocalization.</p>',
        'type' => 'err',
    ],
];
