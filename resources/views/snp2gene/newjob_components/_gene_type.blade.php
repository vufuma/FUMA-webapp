<div class="accordion-item" style="padding:0px;">
    <h2 class="accordion-header" id="headingGT">
        <button class="accordion-button fs-5 collapsed" type="button" data-bs-target="#NewJobGenePanel"
            data-bs-toggle="collapse" aria-expanded="false" aria-controls="NewJobGenePanel">
            4. Gene types
        </button>
    </h2>
    <div class="accordion-collapse collapse" id="NewJobGenePanel" aria-labelledby="headingGT">
        <div class="accordion-body">
            <table class="table table-bordered inputTable" id="NewJobGene" style="width: auto;">
                <tr>
                    <td>Ensembl version</td>
                    <td>
                        <select class="form-select" id="ensembl" name="ensembl">
                            <option selected value="v110">v110</option>
                            <option selected value="v102">v102</option>
                            <!-- REMOVED: no longer supported by biomart option value="v92">v92</option-->
                            <!-- REMOVED: no longer supported by biomart option value="v85">v85</option-->
                        </select>
                    </td>
                    <td>
                        <div class="alert alert-success"
                            style="display: table-cell; padding-top:0; padding-bottom:0;">
                            <i class="fa fa-check"></i> OK.
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>Gene type
                        <a class="infoPop" data-bs-toggle="popover" title="Gene Type"
                            data-bs-content="Setting gene type defines what kind of genes should be included in the gene prioritization. Gene type is based on gene biotype obtained from BioMart (Ensembl). By default, only protein-coding genes are used for mapping.">
                            <i class="fa-regular fa-circle-question fa-lg"></i>
                        </a><br>
                        <span class="info"><i class="fa fa-info"></i> Multiple gene type can be
                            selected.</span>
                    </td>
                    <td>
                        <select multiple class="form-select" name="genetype[]" id="genetype"
                            onchange="window.CheckAll();">
                            <option value="all">All</option>
                            <option selected value="protein_coding">Protein coding</option>
                            <option
                                value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:macro_lncRNA">
                                lncRNA</option>
                            <option value="miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA">ncRNA
                            </option>
                            <option
                                value="lincRNA:antisense:retained_intronic:sense_intronic:sense_overlapping:3prime_overlapping_ncrna:macro_lncRNA:miRNA:piRNA:rRNA:siRNA:snRNA:snoRNA:tRNA:vaultRNA:processed_transcript">
                                Processed transcripts</option>
                            <option
                                value="pseudogene:processed_pseudogene:unprocessed_pseudogene:polymorphic_pseudogene:IG_C_pseudogene:IG_D_pseudogene:ID_V_pseudogene:IG_J_pseudogene:TR_C_pseudogene:TR_D_pseudogene:TR_V_pseudogene:TR_J_pseudogene">
                                Pseudogene</option>
                            <option value="IG_C_gene:TG_D_gene:TG_V_gene:IG_J_gene">IG genes
                            </option>
                            <option value="TR_C_gene:TR_D_gene:TR_V_gene:TR_J_gene">TR genes
                            </option>
                        </select>
                    </td>
                    <td>
                        <div class="alert alert-success"
                            style="display: table-cell; padding-top:0; padding-bottom:0;">
                            <i class="fa fa-check"></i> OK.
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>