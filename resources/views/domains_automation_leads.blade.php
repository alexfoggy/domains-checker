@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <label class="section-title">Domains automation leads</label>

        @if(session('upload_success'))
            <div class="alert alert-success">
                <strong>{{session('new_domains_count')}}</strong> new unique domain(s) were added.
                @if(session('total_processed') > session('new_domains_count'))
                    <div class="text-muted mt-1">
                        Total processed: {{session('total_processed')}} ({{session('total_processed') - session('new_domains_count')}} duplicates skipped)
                    </div>
                @endif
            </div>
        @endif

        <div class="row justify-content-between align-items-center">
            <div class="col-12">
                <a href="{{route('domains')}}" class="btn btn-primary btn-block mg-b-10">Back</a>
            </div>
            <div class="col-12">
                <form action="{{route('domains.automation.leads.store')}}" method="POST">
                    @csrf
                    <div class="form-group">
                        <label class="form-control-label">Domains (one per line): <span class="tx-danger">*</span></label>
                        <textarea class="form-control" name="domains" rows="10"
                                  placeholder="example.com&#10;anotherdomain.net">{{old('domains')}}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 mt-2">Save domains</button>
                </form>
            </div>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-striped mg-b-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    <th>Domain rationg</th>
                    <th>Hunter synced</th>
                </tr>
                </thead>
                <tbody>
                @forelse($domainsAutomationLeads as $lead)
                    <tr>
                        <td>{{$lead->id}}</td>
                        <td>{{$lead->domain}}</td>
                        <td>{{$lead->domain_rationg ?? '-'}}</td>
                        <td>
                            @if($lead->is_hunter_synced)
                                <span class="badge badge-success">Yes</span>
                            @else
                                <span class="badge badge-secondary">No</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center">No domains automation leads yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination-wrapper mt-4">
            <div class="d-flex justify-content-center">
                {{$domainsAutomationLeads->links('pagination::bootstrap-4')}}
            </div>
        </div>
    </div>
@endsection
