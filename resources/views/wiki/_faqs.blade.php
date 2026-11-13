<div class="card mb-2">
    <div class="card-header faq">
        <h4>How many jobs can I submit at once?
            <a href="#" type="button" data-bs-toggle="collapse" data-bs-target="#faq2" class="active" style="float: right; padding-right:20px;">
                <i class="fa fa-chevron-down"></i>
            </a>
        </h4>
    </div>
    <div class="card-body collapse" id="faq2">
        In order to prevent single users to occupy the entire server, there is a job limit of <strong>10 jobs per user</strong>.<br/>
    </div>
</div>

<div class="card mb-2">
    <div class="card-header faq">
        <h4>How many jobs can I save on FUMA?
            <a  href="#" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" class="active" style="float: right; padding-right:20px;">
                <i class="fa fa-chevron-down"></i>
            </a>
        </h4>
    </div>
    <div class="card-body collapse" id="faq3">
        Each user can store at most <strong>100 SNP2GENE jobs </strong> on the FUMA server (policy updated as of v1.6.5).<br/>
        Currently, there is no restriction on the number of GENE2FUNC and Cell Type jobs stored on the FUMA server because these jobs tend to be small in size (but subject to change).<br/>
        All faulty jobs will be deleted after 1 month.<br/>
        All jobs prior to 2023-01-01 have been deleted (policay updated as of v2.0.0).<br/>
    </div>
</div>

<div class="card mb-2">
    <div class="card-header faq">
        <h4>Why is my job in the queue and not starting? 
            <a  href="#" type="button" data-bs-toggle="collapse" data-bs-target="#faq3" class="active" style="float: right; padding-right:20px;">
                <i class="fa fa-chevron-down"></i>
            </a>
        </h4>
    </div>
    <div class="card-body collapse" id="faq3">
        FUMA is a free service we provide for the advancement of science, this also means that there is a limited amount of computational resources to go around. At any one time there can be a maximum of 8 jobs running. If there are many jobs being submitted, the jobs will be in the queue. You can click on the i icon next to your name to view the number of queued jobs. 
        <img src="{!! URL::asset('image/fuma_stats.png') !!}" style="width:500px;">
    </div>
</div>