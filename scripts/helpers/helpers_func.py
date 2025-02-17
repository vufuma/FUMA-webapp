import os, sys, configparser


class Configuration: 
    def __init__(self, filedir): 
        self.filedir = filedir
        self.cfg = None
        self.param = None
        self.get_cfg()
        self.get_param()
        self._qtldir = None
        self._eqtlds = None
        self._sigonly = None
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
        self._qtldir = self.cfg.get("data", "QTL")
        self._eqtlds = self.param.get("eqtlMap", "eqtlMaptss").split(":")
        self._sigonly = int(self.param.get("eqtlMap", "eqtlMapSig"))
        self._eqtlP = float(self.param.get("eqtlMap", "eqtlMapP"))
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