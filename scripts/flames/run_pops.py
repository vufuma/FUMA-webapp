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

def run_pops(filedir, logger):
    try:
        pops_cmd = [
            "python",
            "/opt/pops/pops.py",
            "--gene_annot_path", f"/data/FLAMES/pops_features_full_FUMA_compatible/gene_annots.txt",
            "--feature_mat_prefix", f"/data/FLAMES/pops_features_full_FUMA_compatible/features_munged/pops_features",
            "--num_feature_chunks", "116",
            "--magma_prefix", f"{filedir}/magma",
            "--control_features", f"/data/FLAMES/pops_features_full_FUMA_compatible/control.features",
            "--out_prefix", f"{filedir}/input",
        ]
        
        print("Running PoPS with the following command:" + " ".join(pops_cmd))

        logger.info("Running PoPS")

        subprocess.run(
            pops_cmd,
            check=True,
            capture_output=True,
            text=True
        )

        logger.info("PoPS finished successfully")

    except subprocess.CalledProcessError as e:
        logger.error(
            "PoPS failed", e.returncode
        )
        logger.error("stderr: %s", e.stderr)
        sys.exit(1)

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
    
    
    run_pops(filedir, logger)
    
if __name__ == "__main__":
    main()