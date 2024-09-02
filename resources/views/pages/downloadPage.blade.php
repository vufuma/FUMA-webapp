@extends('layouts.master')

@section('content')
    <div class="container" style="padding-top: 50px;">
        <strong> Please click the download symbol in the third column to download the data files.</strong>
        <table class="table table-bordered" style="width:auto">
            <thead>
                <th>Data source</th>
                <th>Used for</th>
                <th>Download size</th>
            </thead>
            <tbody>
                <tr>
                    <td> <strong> Gene-set files </strong> </td>
                </tr>
                <tr>
                    <td>MSigDB v7.0 gene-set file</td>
                    <td>MAGMA gene-set analysis file used in SNP2GENE from FUMA version 1.3.5d to 1.5.5.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("MSigDB7")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 22M</td>
                </tr>
                <tr>
                    <td>MSigDB v2023.1Hs gene-set file</td>
                    <td>MAGMA gene-set analysis file used in SNP2GENE from FUMA version 1.5.6 onwards.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("MSigDB20231Hs")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 24M</td>
                </tr>
                <tr>
                    <td>MSigDB and GWAS catalog gene-set files </td>
                    <td>Gene-set analysis files used in GENE2FUNC from FUMA version 1.3.5d to 1.5.5.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GENE2FUNC1")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 22M</td>
                </tr>
                <tr>
                    <td>MSigDB and GWAS catalog gene-set files </td>
                    <td>Gene-set analysis files used in GENE2FUNC from FUMA version 1.5.6 onwards.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GENE2FUNC2")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 29M</td>
                </tr>
                <tr>
                    <td> <strong> Gene expression files </strong> </td>
                </tr>
                <tr>
                    <td>GTEx v8 general expression differentially expressed genes</td>
                    <td>The gene-set of the differentially expressed genes in GTEx v8 across 30 tissues. This file is used
                        for GENE2FUNC tissue specificity analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTExDEG30v8")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 6M</td>
                </tr>
                <tr>
                    <td>GTEx v8 specific expression differentially expressed genes</td>
                    <td>The gene-set of the differentially expressed genes in GTEx v8 across 54 tissues. This file is used
                        for GENE2FUNC tissue specificity analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTExDEG54v8")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 12M</td>
                </tr>
                <tr>
                    <td>GTEx v7 general expression differentially expressed genes</td>
                    <td>The gene-set of the differentially expressed genes in GTEx v7 across 30 tissues. This file is used
                        for GENE2FUNC tissue specificity analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTExDEG30v7")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 6M</td>
                </tr>
                <tr>
                    <td>GTEx v7 specific expression differentially expressed genes</td>
                    <td>The gene-set of the differentially expressed genes in GTEx v7 across 54 tissues. This file is used
                        for GENE2FUNC tissue specificity analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTExDEG54v7")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 11M</td>
                </tr>
                <tr>
                    <td>GTEx v6 general expression differentially expressed genes</td>
                    <td>The gene-set of the differentially expressed genes in GTEx v6 across 30 tissues. This file is used
                        for GENE2FUNC tissue specificity analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTExDEG30v6")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 4M</td>
                </tr>
                <tr>
                    <td>GTEx v6 specific expression differentially expressed genes</td>
                    <td>The gene-set of the differentially expressed genes in GTEx v6 across 54 tissues. This file is used
                        for GENE2FUNC tissue specificity analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTExDEG54v6")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 8M</td>
                </tr>
                <tr>
                    <td>GTEx v8 general gene expression </td>
                    <td>The gene expression values in GTEx v8 across 30 tissues. This file is used for SNP2GENE MAGMA Tissue
                        Expression Analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTEx30v8")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 18M</td>
                </tr>
                <tr>
                    <td>GTEx v8 specific gene expression</td>
                    <td>The gene expression values in GTEx v8 across 54 tissues. This file is used for SNP2GENE MAGMA Tissue
                        Expression Analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTEx54v8")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 32M</td>
                </tr>
                <tr>
                    <td>GTEx v7 general gene expression </td>
                    <td>The gene expression values in GTEx v7 across 30 tissues. This file is used for SNP2GENE MAGMA Tissue
                        Expression Analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTEx30v7")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 18M</td>
                </tr>
                <tr>
                    <td>GTEx v7 specific gene expression</td>
                    <td>The gene expression values in GTEx v7 across 54 tissues. This file is used for SNP2GENE MAGMA Tissue
                        Expression Analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTEx54v7")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 30M</td>
                </tr>
                <tr>
                    <td>GTEx v6 general gene expression </td>
                    <td>The gene expression values in GTEx v6 across 30 tissues. This file is used for SNP2GENE MAGMA Tissue
                        Expression Analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTEx30v6")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 16M</td>
                </tr>
                <tr>
                    <td>GTEx v6 specific gene expression</td>
                    <td>The gene expression values in GTEx v6 across 54 tissues. This file is used for SNP2GENE MAGMA Tissue
                        Expression Analysis.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GTEx54v6")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 24M</td>
                </tr>
                <td> <strong> MAGMA gene boundary files </strong> </td>
                </tr>
                <tr>
                    <td> MAGMA gene boundaries Ensembl v85</td>
                    <td>This file defines gene boundaries and is used to map variants to genes in the SNP2GENE MAGMA
                        analysis. Gene locations are GRCh37 and are based on Ensembl v85.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("MAGMAgenev85")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 1M</td>
                </tr>
                <tr>
                    <td> MAGMA gene boundaries Ensembl v92</td>
                    <td>This file defines gene boundaries and is used to map variants to genes in the SNP2GENE MAGMA
                        analysis. Gene locations are GRCh37 and are based on Ensembl v92.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("MAGMAgenev92")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 1M</td>
                </tr>
                <tr>
                    <td> MAGMA gene boundaries Ensembl v102</td>
                    <td>This file defines gene boundaries and is used to map variants to genes in the SNP2GENE MAGMA
                        analysis. Gene locations are GRCh37 and are based on Ensembl v102.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("MAGMAgenev102")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 1M</td>
                </tr>
                <tr>
                    <td> MAGMA gene boundaries Ensembl v110</td>
                    <td>This file defines gene boundaries and is used to map variants to genes in the SNP2GENE MAGMA
                        analysis. Gene locations are GRCh37 and are based on Ensembl v110.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("MAGMAgenev110")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 1M</td>
                </tr>

                <td> <strong> MAGMA files </strong> </td>
                <tr>
                    <td>MAGMA European</td>
                    <td>The reference data files are created from Phase 3 of 1,000 Genomes. The SNP locations in the data are in reference to human genome Build 37.</td>
                    <td>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_EUR_bim")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bim) 808M
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_EUR_bed")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bed) 2.94G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_EUR_fam")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.fam) 12K
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>MAGMA African</td>
                    <td>The reference data files are created from Phase 3 of 1,000 Genomes. The SNP locations in the data are in reference to human genome Build 37.</td>
                    <td>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_AFR_bim")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bim) 1.37G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_AFR_bed")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bed) 6.75G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_AFR_fam")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.fam) 16K
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>MAGMA East Asian</td>
                    <td>The reference data files are created from Phase 3 of 1,000 Genomes. The SNP locations in the data are in reference to human genome Build 37.</td>
                    <td>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_EAS_bim")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bim) 789M
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_EAS_bed")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bed) 2.87G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_EAS_fam")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.fam) 12K
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>MAGMA South Asian</td>
                    <td>The reference data files are created from Phase 3 of 1,000 Genomes. The SNP locations in the data are in reference to human genome Build 37.</td>
                    <td>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_SAS_bim")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bim) 891M
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_SAS_bed")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bed) 3.17G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_SAS_fam")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.fam) 12K
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>MAGMA Middle/South American</td>
                    <td>The reference data files are created from Phase 3 of 1,000 Genomes. The SNP locations in the data are in reference to human genome Build 37.</td>
                    <td>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_AMR_bim")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bim) 951M
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_AMR_bed")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bed) 2.38G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_AMR_fam")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.fam) 8K
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>MAGMA All populations</td>
                    <td>The reference data files are created from Phase 3 of 1,000 Genomes. The SNP locations in the data are in reference to human genome Build 37.</td>
                    <td>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_ALL_bim")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bim) 2.65G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_ALL_bed")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.bed) 49.4G
                        </div>
                    <br>
                        <div class="clickable" onclick='tutorialDownloadVariant("MAGMA_ALL_fam")'>
                            <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" /> (.fam) 61K
                        </div>
                    </td>
                </tr>

                <td> <strong> GRCh38 to rsID </strong> </td>
                </tr>
                <tr>
                    <td> GRCh38 unique ID and rsID file</td>
                    <td>This file is used to convert GRCh38 chromosome and position to rsID in the SNP2GENE analysis. The
                        rsID is based on dbSNP v152.</td>
                    <td class="clickable" onclick='tutorialDownloadVariant("GRCh382rsID")'><img class="fontsvg"
                            src="{{ URL::asset('/image/download.svg') }}" /> 708M</td>
                </tr>
            </tbody>
            <td> <strong> Reference panel data </strong> </td>
            </tr>
            <tr>
                <td> 1000 genomes ALL variants </td>
                <td>This file is a list of the variants included in the 1KG ALL reference panel used in the SNP2GENE
                    analysis.</td>
                <td class="clickable" onclick='tutorialDownloadVariant("ALL")'><img class="fontsvg"
                        src="{{ URL::asset('/image/download.svg') }}" /> 870M</td>
            </tr>
            <tr>
                <td> 1000 genomes AFR variants </td>
                <td>This file is a list of the variants included in the 1KG AFR reference panel used in the SNP2GENE
                    analysis.</td>
                <td class="clickable" onclick='tutorialDownloadVariant("AFR")'><img class="fontsvg"
                        src="{{ URL::asset('/image/download.svg') }}" /> 461M</td>
            </tr>
            <tr>
                <td> 1000 genomes AMR variants </td>
                <td>This file is a list of the variants included in the 1KG AMR reference panel used in the SNP2GENE
                    analysis.</td>
                <td class="clickable" onclick='tutorialDownloadVariant("AMR")'><img class="fontsvg"
                        src="{{ URL::asset('/image/download.svg') }}" /> 305M</td>
            </tr>
            <tr>
                <td> 1000 genomes EAS variants </td>
                <td>This file is a list of the variants included in the 1KG EAS reference panel used in the SNP2GENE
                    analysis.</td>
                <td class="clickable" onclick='tutorialDownloadVariant("EAS")'><img class="fontsvg"
                        src="{{ URL::asset('/image/download.svg') }}" /> 254M</td>
            </tr>
            <tr>
                <td> 1000 genomes EUR variants </td>
                <td>This file is a list of the variants included in the 1KG EUR reference panel used in the SNP2GENE
                    analysis.</td>
                <td class="clickable" onclick='tutorialDownloadVariant("EUR")'><img class="fontsvg"
                        src="{{ URL::asset('/image/download.svg') }}" /> 260M</td>
            </tr>
            <tr>
                <td> 1000 genomes SAS variants </td>
                <td>This file is a list of the variants included in the 1KG SAS reference panel used in the SNP2GENE
                    analysis.</td>
                <td class="clickable" onclick='tutorialDownloadVariant("SAS")'><img class="fontsvg"
                        src="{{ URL::asset('/image/download.svg') }}" /> 287M</td>
            </tr>
            </tbody>
        </table>
        <form method="post" target="_blank" action="/tutorial/download_variants">
            <input type="hidden" name="_token" value="{{ csrf_token() }}">
            <input type="hidden" name="variant_code" id="tutorialDownloadVariantCode" value="" />
            <input type="submit" id="tutorialDownloadVariantSubmit" class="ImgDownSubmit" style="display: none;" />
        </form>
    </div>
@endsection

@section('scripts')
    {{-- Imports from the web --}}
    <!--script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script-->
    <!--script type="text/javascript" src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script-->
    {{-- Imports from the project --}}
    <script type="text/javascript" src="{!! URL::asset('js/tutorial_utils.js') !!}"></script>
    {{-- Hand written ones --}}
    <script type="text/javascript">
        var loggedin = "{{ Auth::check() }}";
    </script>
@endsection
