<form method="post" target="_blank" action="/snp2gene/downloadResults">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="jobID" value="<?php echo $id;?>"/>
    <input type="hidden" name="variant_code" id="tutorialDownloadVariantCode" value="" />
    <input type="submit" id="tutorialDownloadVariantSubmit" class="ImgDownSubmit" style="display: none;" />
</form>
<div id="s2gLogs" class="sidePanel container" style="padding-top:50px; display: none; min-height:80vh;">

    <div class="card">
        <div class="card-body">
            <h4 style="color: #00004d">Download Logs: </h4>
            <div class="clickable" onclick='tutorialDownloadVariant("s2gLogs")'> user.log
                <img class="fontsvg" src="{{ URL::asset('/image/download.svg') }}" />
            </div>
        </div>
    </div>
</div>