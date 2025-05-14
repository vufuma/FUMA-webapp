<div class="card">
    <div class="card-header">
        <div class="card-title">Running Jobs <tab>
                <a id="refreshTable" wire:click="getJobs">
                    <i class="fa fa-refresh"></i>
                </a>
        </div>
    </div>

    <div class="card-body">
        <div>
            @error('selected_jobs')
                {{ $message }}
            @enderror
        </div>
        @if ($jobs->isEmpty())
            <p>There are no jobs in the database.</p>
        @else
            <button type="submit" class="btn btn-default btn-sm" style="float:right; margin-right:20px;"
                wire:click="delJobs">Delete selected jobs</button>
            @if (!is_null($shown_job))
                <button type="submit" class="btn btn-default btn-sm" style="float:right; margin-right:20px;"
                    wire:click="clearSelection">Clear Selection</button>
            @else
                <button type="submit" class="btn btn-default btn-sm" style="float:right; margin-right:20px;"
                    wire:click="showSelected">Show selected</button>
            @endif

            <table class="table ">
                <thead>
                    <tr>
                        <th>Job ID</th>
                        <th>Job name</th>
                        <th>Owner</th>
                        <th>Submit date</th>
                        <th>Started at</th>
                        <th>Completed at</th>
                        <th>Status</th>
                        <th>Job type</th>
                        <th>Select</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($jobs as $job)
                        <tr>
                            <td>{{ $job->jobID }}</td>
                            <td>{{ $job->title }}</td>
                            <td>{{ $job->user->email }}</td>
                            <td>{{ $job->created_at }}</td>
                            <td>{{ $job->started_at }}</td>
                            <td>{{ $job->completed_at }}</td>
                            <td>{{ $job->status }}</td>
                            <td>{{ $job->type }}</td>
                            <td>
                                <input type="checkbox" value="{{ $job->jobID }}" wire:model="selected_jobs">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
        @if (!is_null($shown_job))
            <div class="card-title">Selected Jobs: {{ $shown_job }}</div>

            {{ html()->form('POST', url('admin/db-tools/sync-db-storage/del'))->open() }}
            <input type="hidden" name="selected_listing_jobID" value={{ $shown_job }}>
            <div>
                <button type="submit" class="btn btn-info" style="float: left;">Del</button>
            </div>
            <div style="display: flex; align-items: flex-start;">
                <table class="table table-bordered"
                    style="width: max-content; border-collapse: collapse; margin: 10px;">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" onClick="toggle(this, 'dirs[]')">
                                Directories missing from the db
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($dirs_not_in_db as $dir_not_in_db)
                            <tr>
                                <td>
                                    <input type="checkbox" name="dirs[]" value="{{ $dir_not_in_db['jobID'] }}">
                                    {{ $dir_not_in_db['jobID'] }} | {{ $dir_not_in_db['type'] }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                <table class="table table-bordered"
                    style="width: max-content; border-collapse: collapse; margin: 10px;">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" onClick="toggle(this, 'db_entries[]')">
                                Db entries missign from storage
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($jobs_not_in_dir as $job_not_in_dir)
                            <tr>
                                <td>
                                    <input type="checkbox" name="db_entries[]" value="{{ $job_not_in_dir['jobID'] }}">
                                    {{ $job_not_in_dir['jobID'] }} |
                                    {{ $job_not_in_dir['type'] }} |
                                    {{ $job_not_in_dir['status'] }}
                                    @if ($job_not_in_dir['is_public'])
                                        | P
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ html()->form()->close() }}
        @endif
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
    </div>
</div>
