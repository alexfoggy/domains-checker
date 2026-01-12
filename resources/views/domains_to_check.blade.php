@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <div>
            <div class=""><a href="{{route('domains.to.check.avalile', request()->only('tag'))}}" class="btn btn-primary mb-4">Only
                    available</a>
            </div>
            <div>
                <a href="{{route('domains.to.check.restart')}}" class="btn btn-secondary mb-4">Restart verification</a>
            </div>
        </div>
        <label class="section-title">Domains to check</label>

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
        
        @if(isset($tags) && $tags->count() > 0)
        <div class="row mb-3">
            <div class="col-12">
                <form method="GET" action="{{route('domains.to.check')}}" class="d-inline">
                    <div class="form-group mb-0">
                        <label class="form-control-label">Filter by tag:</label>
                        <div class="d-flex">
                            <select name="tag" class="form-control" style="max-width: 300px;" onchange="this.form.submit()">
                                <option value="">All tags</option>
                                @foreach($tags as $tagOption)
                                    <option value="{{$tagOption}}" {{request('tag') == $tagOption ? 'selected' : ''}}>{{$tagOption}}</option>
                                @endforeach
                            </select>
                            @if(request('tag'))
                                <a href="{{route('domains.to.check')}}" class="btn btn-secondary ml-2">Clear filter</a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>
        </div>
        @endif
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
                                <span class="badge badge-info">{{$domain->tag ?? 'agency'}}</span>
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
            {{$domainsToCheck->links()}}
        </div><!-- bd -->
    </div><!-- section-wrapper -->

    <script>
        let button = document.querySelector('.checkAll');
        button.addEventListener('click', function (e) {
            if (button.checked) {
                $(':checkbox').each(function () {
                    this.checked = true;
                });
            } else {
                $(':checkbox').each(function () {
                    this.checked = false;
                });
            }
        });
    </script>
@endsection
