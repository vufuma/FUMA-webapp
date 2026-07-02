import subprocess
import logging
import argparse
import os
import sys
import configparser

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
    
    try:
        drugsets_cmd = [
            "python",
            "/app/drugsets-dev/drugsets.py",
            "--raw_file", f"{filedir}/magma.genes.raw",
            "--drugsets", f"{drugsets_selection}",
            "--magma", "/app/magma",
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
        sys.exit(2)
    
if __name__ == "__main__":
    main()