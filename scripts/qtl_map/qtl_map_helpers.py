import tabix
import re
import os
import pandas as pd
import numpy as np

def eqtl_tabix(region, tb):
	qtls = []
	try:
		tmp = tb.querys(region)
	except:
		print("Tabix failed for region "+region)
	else:
		for l in tmp:
			qtls.append(l[0:9])
	return qtls


def process_loci(tb, loci, locus, snps, config_class):
    chrom = loci.iloc[locus,1]
    start = loci.iloc[locus,2]
    end = loci.iloc[locus,3]
    qtls = eqtl_tabix(str(chrom)+":"+str(start)+"-"+str(end), tb)
    qtls = pd.DataFrame(qtls, columns=['chr', 'pos', 'a1', 'a2', 'ta', 'gene', 'stats', 'p', 'fdr'])

    ### filter on qtls based on position
    qtls = qtls[qtls.iloc[:,1].astype('int').isin(snps[snps.iloc[:,1]==chrom].iloc[:,2])] #get the qtls that are in the snps.txt file
    if len(qtls)==0: 
        return None

    ### filter by P/FDR
    #qtls.iloc[:,6:] = qtls.iloc[:,6:].apply(pd.to_numeric, errors='coerce', axis=1)
    if config_class._sigonly == 1:
        qtls = qtls[pd.to_numeric(qtls.iloc[:,8], errors='coerce')<0.05]
    else:
        qtls = qtls[pd.to_numeric(qtls.iloc[:,7], errors='coerce')<config_class._eqtlP]
    if len(qtls)==0:
        return None

    ### assign uniqID
    ## if qtls do not have alleles, take uniqID from snps
    ## For multi allelic SNPs, duplicated qtls (for later use in gene mapping)

    if qtls.iloc[0, 2] == "NA" and qtls.iloc[0, 3] == "NA":
        qtls.iloc[:, 1] = qtls.iloc[:, 1].astype(int)
        qtls = qtls.merge(snps.loc[snps.iloc[:, 1] == chrom, ["pos", "uniqID"]], on="pos", how="left")
        qtls = qtls[qtls.uniqID.isin(snps.uniqID)]

    elif qtls.iloc[0, 2] == "NA" or qtls.iloc[0, 3] == "NA":
        # Generate uniqID1 and uniqID2 using vectorized string operations
        snps[['uniqID1', 'uniqID2']] = snps['uniqID'].str.split(":", expand=True).iloc[:, [0, 1, 3]].agg(':'.join, axis=1), \
                                    snps['uniqID'].str.split(":", expand=True).drop(columns=2).agg(':'.join, axis=1)

        qtls["uniqID1"] = qtls.iloc[:, 0].astype(str) + ":" + qtls.iloc[:, 1].astype(str) + ":" + \
                        np.where(qtls.iloc[:, 2] == "NA", qtls.iloc[:, 3], qtls.iloc[:, 2])

        # Match using uniqID1
        matched_uniqID1 = qtls[qtls.uniqID1.isin(snps.uniqID1)]
        matched_uniqID1 = matched_uniqID1.merge(snps.loc[snps.uniqID1.isin(matched_uniqID1.uniqID1), ["uniqID1", "uniqID"]],
                                                on="uniqID1", how="left").drop(columns="uniqID1")

        # Match using uniqID2
        qtls = qtls[qtls.uniqID1.isin(snps.uniqID2)]
        qtls = qtls.merge(snps.loc[snps.uniqID2.isin(qtls.uniqID1), ["uniqID2", "uniqID"]],
                        left_on="uniqID1", right_on="uniqID2", how="left").drop(columns=["uniqID1", "uniqID2"])

        # Combine results
        qtls = pd.concat([qtls, matched_uniqID1], ignore_index=True)

    else:
        qtls.iloc[:, 2:4] = np.sort(qtls.iloc[:, 2:4], axis=1)  # Sort in-place
        qtls["uniqID"] = qtls.iloc[:, 0].astype(str) + ":" + qtls.iloc[:, 1].astype(str) + ":" + qtls.iloc[:, 2] + ":" + qtls.iloc[:, 3]
        qtls = qtls[qtls.uniqID.isin(snps.uniqID)]

    return qtls

def align_qtl(qtls): #TODO: add the condition here. Now just add NA placeholder
    qtls["RiskIncAllele"] = np.nan
    qtls["alignedDirection"] = np.nan
    return qtls
    

def process_qtl(fqtl, config_class, loci, snps, fout):
    # reg = re.match(r'(.+)\/(.+).txt.gz', fqtl)
    # db = reg.group(1)
    # ts = reg.group(2)
    db = fqtl.split("_v6_")[0]
    ts = fqtl.split("_v6_")[1]
    tb = tabix.open(os.path.join(config_class._qtldir, db, "v6", ts + ".txt.gz"))
    for locus in range(len(loci)):
        qtls = process_loci(tb=tb, loci=loci, locus=locus, snps=snps, config_class=config_class)
        if qtls is not None:
            aligned_qtls = align_qtl(qtls)
            aligned_qtls['db'] = db
            aligned_qtls['tissue'] = ts
            aligned_qtls = aligned_qtls[["uniqID", "db", "tissue", "gene", "ta", "p", "stats", "fdr", "RiskIncAllele", "alignedDirection"]]
            aligned_qtls.to_csv(fout, header=False, index=False, mode='a', na_rep="NA", sep="\t", float_format="%.5f")
            
            
def process_ensg(config_class):
    ENSG = pd.read_csv(os.path.join(config_class._ENSG, config_class._ensembl, config_class._ENSGfile), sep="\t")
    # Vectorized chromosome conversion
    ENSG.loc[ENSG["chromosome_name"] == "X", "chromosome_name"] = "23"
    ENSG["chromosome_name"] = ENSG["chromosome_name"].astype(int)

    # Convert other columns efficiently
    ENSG[["start_position", "end_position"]] = ENSG[["start_position", "end_position"]].astype(int)

    
    if config_class._genetype != "all":
        genetype = set(config_class._genetype.split(":"))
        
    ENSG = ENSG[ENSG["gene_biotype"].isin(genetype)]
    
    # Exclude MHC genes if required
    if config_class._exMHC == 1:
        start = ENSG.loc[ENSG["external_gene_name"] == "MOG", "start_position"].values[0]
        end = ENSG.loc[ENSG["external_gene_name"] == "COL11A2", "end_position"].values[0]
    
    # Adjust MHC boundaries
    if config_class._extMHC != "NA":
        extMHC = list(map(int, config_class._extMHC.split("-")))
        start = min(start, extMHC[0])
        end = max(end, extMHC[1])

    # Optimized filtering using .query() and avoiding unnecessary selections
    ENSG = ENSG.query(
        "not (chromosome_name == 6 and ((end_position >= @start and end_position <= @end) or "
        "(start_position >= @start and start_position <= @end)))"
    )
    
    return ENSG

def do_eqtl_mapping(config_class, eqtl_fp, snps_fp):
    eqtl = pd.read_csv(eqtl_fp, sep="\t", keep_default_na=False)
    snps = pd.read_csv(snps_fp, sep="\t")
    ENSG = process_ensg(config_class)
    if eqtl.shape[0] > 0: 
        eqtl = eqtl.query("gene.isin(@ENSG['ensembl_gene_id'])")
        eqtl['chr'] = eqtl['uniqID'].map(snps.set_index('uniqID')['chr'])
        eqtl['pos'] = eqtl['uniqID'].map(snps.set_index('uniqID')['pos'])
        eqtl['symbol'] = eqtl['gene'].map(ENSG.set_index('ensembl_gene_id')['external_gene_name'])
        eqtl['eqtlMapFilt'] = 1
        return eqtl
    # print(eqtl.head)
        
    #TODO: Implement the different filtering
            