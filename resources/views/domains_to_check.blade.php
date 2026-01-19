@extends('layouts.index')

@section('content')
    @if(session('upload_success'))
    <!-- Upload Success Modal -->
    <div class="modal fade show" id="uploadSuccessModal" tabindex="-1" role="dialog" style="display: block; padding-right: 17px;">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Upload Successful</h5>
                    <button type="button" class="close" onclick="closeUploadModal()" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><strong>{{session('new_domains_count')}}</strong> new unique domain(s) were added successfully.</p>
                    @if(session('total_processed') > session('new_domains_count'))
                        <p class="text-muted">Total processed: {{session('total_processed')}} ({{session('total_processed') - session('new_domains_count')}} were duplicates)</p>
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="closeUploadModal()">OK</button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal-backdrop fade show" id="uploadModalBackdrop"></div>
    @endif
    <div class="section-wrapper mg-t-20">
        <div>
            <div class=""><a href="{{route('domains.to.check.avalile', request()->only(['tag', 'search']))}}" class="btn btn-primary mb-4">Only
                    available</a>
            </div>
            <div>
                <a href="{{route('domains.to.check.restart')}}" class="btn btn-secondary mb-4">Restart verification</a>
            </div>
        </div>
        <label class="section-title">Domains to check</label>
        
        @if(isset($domainsLast7Days) || isset($domainsPending) || isset($domainsDone) || isset($domainsTaken))
        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <strong>Statistics:</strong>
                    <span class="badge badge-primary ml-2">{{$domainsLast7Days ?? 0}}</span> domains added in the last 7 days
                    <span class="badge badge-warning ml-2">{{$domainsPending ?? 0}}</span> pending (status 0)
                    <span class="badge badge-success ml-2">{{$domainsDone ?? 0}}</span> done (status 1)
                    <span class="badge badge-danger ml-2">{{$domainsTaken ?? 0}}</span> taken (status 2)
                </div>
            </div>
        </div>
        @endif

        <div class="row justify-content-between align-items-center">
            <div class="col-12"><a href="{{route('domains')}}" class="btn btn-primary btn-block mg-b-10">Back</a></div>
            <div class="col-12">
                <form action="{{route('domains.to.create')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Domain to check: <span class="tx-danger">*</span></label>
                        <div>
                                <textarea class="form-control" type="text" name="domains" value="{{old('domain')}}"
                                          placeholder="Enter domain"> </textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-control-label">Tag: <span class="tx-danger">*</span></label>
                        <div>
                            <input class="form-control" type="text" name="tag" value="{{old('tag', 'agency')}}"
                                   placeholder="Enter tag (e.g., agency)">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Upload</button>
                </form>
            </div><!-- col-sm-3 -->

        </div>
        
        <div class="row mb-3">
            <div class="col-12">
                <form method="GET" action="{{route('domains.to.check')}}" class="d-inline w-100">
                    <div class="row align-items-end">
                        @if(isset($tags) && $tags->count() > 0)
                        <div class="col-md-4">
                            <div class="form-group mb-0">
                                <label class="form-control-label">Filter by tag:</label>
                                <select name="tag" class="form-control" onchange="this.form.submit()">
                                    <option value="">All tags</option>
                                    @foreach($tags as $tagOption)
                                        <option value="{{$tagOption}}" {{request('tag') == $tagOption ? 'selected' : ''}}>{{$tagOption}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        @endif
                        <div class="col-md-6">
                            <div class="form-group mb-0">
                                <label class="form-control-label">Search by domain:</label>
                                <div class="d-flex">
                                    <input type="text" name="search" class="form-control" value="{{request('search')}}" placeholder="Enter domain name...">
                                    <button type="submit" class="btn btn-primary ml-2">Search</button>
                                    @if(request('search') || request('tag'))
                                        <a href="{{route('domains.to.check')}}" class="btn btn-secondary ml-2">Clear</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="table-responsive">
            <form action="{{route('domain.checked')}}" method="POST">
                @csrf
                <table class="table table-striped mg-b-0">
                    <thead>
                    <tr>
                        <th class="bg-dark"><input type="checkbox" class="checkAll"></th>
                        <th>ID</th>
                        <th>Domain name</th>
                        <th>Tag</th>
                        <th style="text-align: right">Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($domainsToCheck as $key => $domain)
                        <tr>
                            <td>@if(!$domain->is_checked)
                                    <input type="checkbox" name="checkbox[{{$domain->id}}]">
                                @endif</td>
                            <th scope="row">{{$domain->id}}</th>
                            <td>
                                <a href="https://app.ahrefs.com/site-explorer/overview/v2/subdomains/live?target={{$domain->domain}}"
                                   target="_blank">{{$domain->domain}}</a></td>
                            <td>
                                <div class="tag-edit-container" data-domain-id="{{$domain->id}}">
                                    <span class="tag-display">
                                        <span class="badge badge-info">{{$domain->tag ?? 'agency'}}</span>
                                        <button type="button" class="btn btn-sm btn-link p-0 ml-1 edit-tag-btn" style="font-size: 12px;">✏️</button>
                                    </span>
                                    <span class="tag-edit" style="display: none;">
                                        <input type="text" class="form-control form-control-sm d-inline-block" style="width: 120px;" value="{{$domain->tag ?? 'agency'}}" data-original-tag="{{$domain->tag ?? 'agency'}}">
                                        <button type="button" class="btn btn-sm btn-success save-tag-btn ml-1">Save</button>
                                        <button type="button" class="btn btn-sm btn-secondary cancel-tag-btn ml-1">Cancel</button>
                                    </span>
                                </div>
                            </td>
                            <td align="right">
                                @if($domain->is_checked)
                                    <span class="btn-danger text-white px-2 py-1">Checked</span>
                                @endif
                                @if($domain->status == 1)
                                    <span class="btn-success text-white px-2 py-1">Avalible</span>
                                @elseif($domain->status == 2)
                                    <span class="btn-danger text-white px-2 py-1">Taken</span>
                                @else
                                    <span class="btn-warning text-white px-2 py-1">In proccess</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
                <button class="btn btn-warning my-4">Set domains checked</button>
            </form>
            <div class="pagination-wrapper mt-4">
                <div class="d-flex justify-content-center">
                    {{$domainsToCheck->links('pagination::bootstrap-4')}}
                </div>
            </div>
        </div><!-- bd -->
    </div><!-- section-wrapper -->

    <script>
        // Function to close upload success modal
        function closeUploadModal() {
            const modal = document.getElementById('uploadSuccessModal');
            const backdrop = document.getElementById('uploadModalBackdrop');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
            }
            if (backdrop) {
                backdrop.remove();
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check all functionality
        let button = document.querySelector('.checkAll');
            if (button) {
        button.addEventListener('click', function (e) {
                    const checkboxes = document.querySelectorAll('input[type="checkbox"]:not(.checkAll)');
                    checkboxes.forEach(function(checkbox) {
                        checkbox.checked = button.checked;
                    });
                });
            }

            // Tag editing functionality
            document.addEventListener('click', function(e) {
                // Edit button
                if (e.target.classList.contains('edit-tag-btn') || e.target.closest('.edit-tag-btn')) {
                    const btn = e.target.classList.contains('edit-tag-btn') ? e.target : e.target.closest('.edit-tag-btn');
                    const container = btn.closest('.tag-edit-container');
                    const display = container.querySelector('.tag-display');
                    const edit = container.querySelector('.tag-edit');
                    const input = container.querySelector('input');
                    
                    display.style.display = 'none';
                    edit.style.display = 'inline-block';
                    input.focus();
                }

                // Cancel button
                if (e.target.classList.contains('cancel-tag-btn') || e.target.closest('.cancel-tag-btn')) {
                    const btn = e.target.classList.contains('cancel-tag-btn') ? e.target : e.target.closest('.cancel-tag-btn');
                    const container = btn.closest('.tag-edit-container');
                    const display = container.querySelector('.tag-display');
                    const edit = container.querySelector('.tag-edit');
                    const input = container.querySelector('input');
                    const originalTag = input.getAttribute('data-original-tag');
                    
                    input.value = originalTag;
                    edit.style.display = 'none';
                    display.style.display = 'inline-block';
                }

                // Save button
                if (e.target.classList.contains('save-tag-btn') || e.target.closest('.save-tag-btn')) {
                    const btn = e.target.classList.contains('save-tag-btn') ? e.target : e.target.closest('.save-tag-btn');
                    const container = btn.closest('.tag-edit-container');
                    const domainId = container.getAttribute('data-domain-id');
                    const input = container.querySelector('input');
                    const newTag = input.value.trim();
                    const originalTag = input.getAttribute('data-original-tag');
                    const cancelBtn = container.querySelector('.cancel-tag-btn');
                    const badge = container.querySelector('.badge-info');

                    if (newTag === '') {
                        alert('Tag cannot be empty');
                        return;
                    }

                    if (newTag === originalTag) {
                        container.querySelector('.tag-edit').style.display = 'none';
                        container.querySelector('.tag-display').style.display = 'inline-block';
                        return;
                    }

                    // Disable buttons during save
                    btn.disabled = true;
                    btn.textContent = 'Saving...';
                    cancelBtn.disabled = true;

                    // Create form data
                    const formData = new FormData();
                    formData.append('_token', '{{csrf_token()}}');
                    formData.append('tag', newTag);

                    // Make AJAX request
                    fetch('/domain/' + domainId + '/update-tag', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update the badge text
                            badge.textContent = newTag;
                            input.setAttribute('data-original-tag', newTag);
                            container.querySelector('.tag-edit').style.display = 'none';
                            container.querySelector('.tag-display').style.display = 'inline-block';
                        } else {
                            alert('Error updating tag. Please try again.');
                            btn.disabled = false;
                            btn.textContent = 'Save';
                            cancelBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Error updating tag. Please try again.');
                        btn.disabled = false;
                        btn.textContent = 'Save';
                        cancelBtn.disabled = false;
                    });
                }
            });

            // Allow Enter key to save
            document.addEventListener('keypress', function(e) {
                if (e.target.matches('.tag-edit input')) {
                    if (e.key === 'Enter') {
                        e.target.closest('.tag-edit-container').querySelector('.save-tag-btn').click();
                    }
                }
            });

            // Allow Escape key to cancel
            document.addEventListener('keydown', function(e) {
                if (e.target.matches('.tag-edit input')) {
                    if (e.key === 'Escape') {
                        e.target.closest('.tag-edit-container').querySelector('.cancel-tag-btn').click();
                    }
                }
            });
        });
    </script>
@endsection
