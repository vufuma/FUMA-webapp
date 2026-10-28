import subprocess
import logging
import argparse
import os
import sys
import configparser
import pandas as pd

def parse_args():
    parser = argparse.ArgumentParser()
    parser.add_argument('--filedir', required=True, help="Path to input directory.")
    args = parser.parse_args()
    
    return args

def main():
    args = parse_args()
    
    # Setting up parameters
    filedir = args.filedir
    
    cfg = configparser.ConfigParser()
    cfg.read(os.path.dirname(os.path.realpath(__file__))+'/app.config')

    param = configparser.RawConfigParser()
    param.optionxform = str
    param.read(os.path.join(filedir, 'params.config'))
    
    # Setting up the log file
    logging.basicConfig(
        filename=os.path.join(filedir, "job.log"),
        filemode="a",
        level=logging.INFO,
        format="%(asctime)s [%(levelname)s] %(name)s: %(message)s",
    )
    logger = logging.getLogger(__name__)
    
    # check if the file magma.genes.raw exists
    if not os.path.exists(os.path.join(filedir, "magma.genes.raw")):
        logger.error(f"File {os.path.join(filedir, 'magma.genes.raw')} does not exist. You need to click on the 'Perform MAGMA' button first to generate this file before running drugsets.")
        sys.exit(1)
        
    drugsets_selection = param.get('magma', 'drugsets_selection')
    dgidb = param.get('magma', 'dgidb')
    conditional_drug = param.get('magma', 'conditional_drug')
    enrich = param.get('magma', 'enrich')
    correct_cov = param.get('magma', 'correct_cov')
    min_set_size_drug = param.get('magma', 'min_set_size_drug')
    min_sample_size = param.get('magma', 'min_sample_size')
    multiple_testing_drug = param.get('magma', 'multiple_testing_drug')
    use_pops_drug = param.get('magma', 'use_pops_drug')
    moasets = param.get('magma', 'moasets')
    conditional_moa = param.get('magma', 'conditional_moa')
    min_set_size_moa = param.get('magma', 'min_set_size_moa')
    multiple_testing_moa = param.get('magma', 'multiple_testing_moa')
    use_pops_moa = param.get('magma', 'use_pops_moa')

    if use_pops_drug == "yes":
        if not os.path.exists(os.path.join(filedir, "input.preds")):
            logger.error(f"File {os.path.join(filedir, 'input.preds')} does not exist. This file needs to be present to run drug sets with the pops option.")
            sys.exit(2)
        try:
            drugsets_cmd = [
            "python",
            "/app/drugsets/drugsets.py",
            "--raw_file", f"{filedir}/magma.genes.raw",
            "--drugsets", f"{drugsets_selection}",
            "--magma", "/app/magma",
            "--conditional", f"{conditional_drug}",
            "--correct_cov", f"{correct_cov}",
            "--setsize", f"{min_set_size_drug}",
            "--nsize", f"{min_sample_size}",
            "--correct", f"{multiple_testing_drug}",
            "--use_pops", f"{filedir}/magma.genes.out", f"{filedir}/input.preds",
            "--adj_pops", "no",
            "--out", f"{filedir}/drugsets_output",
            "--gene_id", "ensembl"
            ]
        
            logger.info(f"Running drugsets with command {drugsets_cmd}")

            subprocess.run(
            drugsets_cmd,
            check=True,
            capture_output=True,
            text=True
            )
            logger.info("Drugsets analysis is successful")
        except subprocess.CalledProcessError as e:
            logger.error(
                "drugsets failed"
            )
            logger.error("stderr: %s", e.stderr)
            sys.exit(3)
            
    else:
        logger.info("PoPS is not used, running drugsets without PoPS")
        if enrich == "None":
            logger.info("Enrichment is not selected, running drugsets without enrichment")
            try:
                drugsets_cmd = [
                "python",
                "/app/drugsets/drugsets.py",
                "--raw_file", f"{filedir}/magma.genes.raw",
                "--drugsets", f"{drugsets_selection}",
                "--dgidb_filter", f"{dgidb}",
                "--magma", "/app/magma",
                "--conditional", f"{conditional_drug}",
                "--correct_cov", f"{correct_cov}",
                "--setsize", f"{min_set_size_drug}",
                "--nsize", f"{min_sample_size}",
                "--gsa_correct", f"{multiple_testing_drug}",
                "--out", f"{filedir}/drugsets_output",
                "--gene_id", "ensembl"
                ]
                
                logger.info(f"Running drugsets with command {drugsets_cmd}")

                subprocess.run(
                    drugsets_cmd,
                    check=True,
                    capture_output=True,
                    text=True
                )
                logger.info("Drugsets analysis is successful")
            except subprocess.CalledProcessError as e:
                logger.error(
                    "drugsets failed"
                )
                logger.error("stderr: %s", e.stderr)
                sys.exit(4)
        else:
            logger.info("Enrichment is selected, running drugsets with enrichment")
            try:
                drugsets_cmd = [
                "python",
                "/app/drugsets/drugsets.py",
                "--raw_file", f"{filedir}/magma.genes.raw",
                "--drugsets", f"{drugsets_selection}",
                "--dgidb_filter", f"{dgidb}",
                "--magma", "/app/magma",
                "--conditional", f"{conditional_drug}",
                "--correct_cov", f"{correct_cov}",
                "--setsize", f"{min_set_size_drug}",
                "--nsize", f"{min_sample_size}",
                "--gsa_correct", f"{multiple_testing_drug}",
                "--enrich", "atc3",
                "--correct_cov", f"{correct_cov}",
                "--out", f"{filedir}/drugsets_output",
                "--gene_id", "ensembl"
                ]
                
                logger.info(f"Running drugsets with command {drugsets_cmd}")

                subprocess.run(
                    drugsets_cmd,
                    check=True,
                    capture_output=True,
                    text=True
                )
                logger.info("Drugsets analysis is successful")
            except subprocess.CalledProcessError as e:
                logger.error(
                    "drugsets failed"
                )
                logger.error("stderr: %s", e.stderr)
                sys.exit(4)
    
    # remove the # from the output file for formatting into a table
    logger.info("Removing the # from the output file for formatting into a table")
    with open(f"{filedir}/drugsets_output.{drugsets_selection}.drugs.out", "r") as infile, open(f"{filedir}/drugs.out.fmt", "w") as outfile:
        for line in infile:
            if not line.startswith("#"):
                outfile.write(line)
    #TO DELETE: Post-processing drugsets results is not needed anymore since the output is already in the correct format
    # logger.info("Post-processing drugsets results")
    # with open(f"{filedir}/drugsets_output.drug.gsa.out.fmt", "w") as out:
    #     grep = subprocess.Popen(
    #         ["grep", "-v", "#", f"{filedir}/drugsets_output.drug.gsa.out"],
    #         stdout=subprocess.PIPE,
    #         text=True,
    #     )

    #     awk = subprocess.run(
    #         ["awk", '{print $8"\t"$2"\t"$3"\t"$4"\t"$5"\t"$6"\t"$7}'],
    #         stdin=grep.stdout,
    #         stdout=out,
    #         text=True,
    #         check=True,
    #     )

    #     grep.stdout.close()
    #     grep.wait()
        
    # drugsets_results = pd.read_csv(f"{filedir}/drugsets_output.drug.gsa.out.fmt", sep="\t")
    # n_obs = drugsets_results.shape[0]
    # drugsets_results_sig = drugsets_results[drugsets_results["P"] <= (0.05/n_obs)]
    # drugsets_results_sig.to_csv(f"{filedir}/drugsets_output.drug.gsa.out.fmt.sig", sep="\t", index=False)
    
    # moa
    if moasets != "none":
        if use_pops_moa == "yes":
            logger.info("Running moa with PoPS")
            try:
                moasets_cmd = [
                "python",
                "/app/drugsets/drugsets.py",
                "--raw_file", f"{filedir}/magma.genes.raw",
                "--drugsets", f"{moasets}",
                "--magma", "/app/magma",
                "--conditional", f"{conditional_moa}",
                "--setsize", f"{min_set_size_moa}",
                "--correct", f"{multiple_testing_moa}",
                "--use_pops", f"{filedir}/magma.genes.out", f"{filedir}/input.preds",
                "--adj_pops", "no",
                "--out", f"{filedir}/moasets_output",
                "--gene_id", "ensembl"
                ]
            
                logger.info(f"Running moasets with command {moasets_cmd}")

                subprocess.run(
                    moasets_cmd,
                    check=True,
                    capture_output=True,
                    text=True
                )
                logger.info("Moasets analysis is successful")
            except subprocess.CalledProcessError as e:
                logger.error(
                    "moasets failed"
                )
                logger.error("stderr: %s", e.stderr)
                sys.exit(5)
        else:
            logger.info("Running moa without PoPS")
            try:
                moasets_cmd = [
                "python",
                "/app/drugsets/drugsets.py",
                "--raw_file", f"{filedir}/magma.genes.raw",
                "--drugsets", f"{moasets}",
                "--magma", "/app/magma",
                "--conditional", f"{conditional_moa}",
                "--setsize", f"{min_set_size_moa}",
                "--gsa_correct", f"{multiple_testing_moa}",
                "--out", f"{filedir}/moasets_output",
                "--gene_id", "ensembl"
                ]
            
                logger.info(f"Running moasets with command {moasets_cmd}")

                subprocess.run(
                    moasets_cmd,
                    check=True,
                    capture_output=True,
                    text=True
                )
                logger.info("Moasets analysis is successful")
            except subprocess.CalledProcessError as e:
                logger.error(
                    "moasets failed"
                )
                logger.error("stderr: %s", e.stderr)
                sys.exit(6)
        
        # remove the # from the output file for formatting into a table
        logger.info("Removing the # from the output moa file for formatting into a table")
        with open(f"{filedir}/moasets_output.{moasets}.moa.out", "r") as infile, open(f"{filedir}/moa.out.fmt", "w") as outfile:
            for line in infile:
                if not line.startswith("#"):
                    outfile.write(line)
    

if __name__ == "__main__":
    main()