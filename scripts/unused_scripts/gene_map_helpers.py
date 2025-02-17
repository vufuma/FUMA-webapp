import os
import configparser
import sys
import pandas as pd


class Configuration: 
    def __init__(self, filedir): 
        self.filedir = filedir
        self.cfg = None
        self.param = None
        self.get_cfg()
        self.get_param()
        self._ENSG = None
        self._ENSGfile = None
        self._ensembl = None
        self._genetype = None
        self._exMHC = None
        self._extMHC = None
        self._eqtlMap = None
        self._eqtlMaptss = None
        self._eqtlMapSigeqtl = None
        self._eqtlP = None
        self._eqtlMapCADDth = None
        self._eqtlMapRDBth = None
        self._eqtlMapChr15 = None
        self._eqtlMapChr15Max = None
        self._eqtlMapChr15Meth = None
        
        self.get_values()
        
        
    def get_cfg(self): 
        app_config_fp = os.path.join(os.path.dirname(os.path.realpath(__file__)), "app.config")
        if os.path.exists(app_config_fp):
            self.cfg = configparser.ConfigParser()
            self.cfg.read(app_config_fp)
        else:
            # logger.error("app.config file is not found in the script folder")
            sys.exit("app.config file is not found in the script folder")
    
    def get_param(self):
        params_config_fp = os.path.join(self.filedir, 'params.config')
        if os.path.exists(params_config_fp):
            self.param = configparser.ConfigParser()
            self.param.read(os.path.join(self.filedir, 'params.config'))
        else: 
            # logger.error("params.config file is not found in the job folder")
            sys.exit("params.config file is not found in the job folder")
            
    def get_values(self):
        self._ENSG = self.cfg.get("data", "ENSG") 
        self._ENSGfile = self.cfg.get("data", "ENSGfile")
        self._ensembl = self.param.get("params", "ensembl")
        self._genetype = self.param.get("params", "genetype")
        self._exMHC = int(self.param.get("params", "exMHC"))
        self._extMHC = self.param.get("params", "extMHC")
        self._eqtlMap = int(self.param.get("eqtlMap", "eqtlMap"))
        self._eqtlMaptss = self.param.get("eqtlMap", "eqtlMaptss")
        self._eqtlMapSigeqtl = int(self.param.get("eqtlMap", "eqtlMapSig"))
        self._eqtlP = int(self.param.get("eqtlMap", "eqtlMapP"))
        self._eqtlMapCADDth = float(self.param.get("eqtlMap", "eqtlMapCADDth"))
        self._eqtlMapRDBth = self.param.get("eqtlMap", "eqtlMapRDBth")
        self._eqtlMapChr15 = self.param.get("eqtlMap", "eqtlMapChr15")
        self._eqtlMapChr15Max = self.param.get("eqtlMap", "eqtlMapChr15Max")
        self._eqtlMapChr15Meth = self.param.get("eqtlMap", "eqtlMapChr15Meth")
            
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
    # print(eqtl.head)
        
    #TODO: Implement the different filtering
    
    return eqtl
    
            
