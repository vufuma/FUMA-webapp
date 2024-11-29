// This module should be imported on the snp2gene blade page
// It initializes the page context with side effects.
// The global window.setS2GPageState should be called first
// with the php macro values for the rendered page
console.log("Initialize S2G page");

import { CheckAll, loadParams, ciFileDel, ciFileCheck } from "../utils/NewJobParameters.js";
window.CheckAll = CheckAll;
window.loadParams = loadParams;
window.ciFileDel = ciFileDel;
window.ciFileCheck = ciFileCheck;
import { ImgDown, circosDown, Chr15Select, expImgDown } from "../utils/s2g_results.js"
window.ImgDown = ImgDown;
window.circosDown = circosDown;
window.Chr15Select = Chr15Select;
window.expImgDown = expImgDown;
import { loadGeneMap } from "../utils/geneMapParameters.js";
window.loadGeneMap = () => loadGeneMap(window.subdir);
import { g2fbtn, checkPublish, checkPublishInput } from "../utils/snp2gene.js";
window.g2fbtn = g2fbtn;
window.checkPublish = checkPublish;
window.checkPublishInput = checkPublishInput;
import { geneMapCheckAll } from "../utils/geneMapParameters.js";
window.geneMapCheckAll = geneMapCheckAll;
import { S2GPageState as pageState}  from "../pages/pageStateComponents.js";

import { NewJobSetup } from "../utils/NewJobParameters.js";
import { Snp2GeneSetup } from "../utils/snp2gene.js";
import { GeneMapSetup } from "../utils/geneMapParameters.js";
import { SidebarSetup } from "../utils/sidebar.js"
$(function(){
    SidebarSetup();
    NewJobSetup();
    Snp2GeneSetup();
    GeneMapSetup(pageState.get("subdir"));
});
