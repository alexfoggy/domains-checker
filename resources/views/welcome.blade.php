@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <label class="section-title">Domains for parsing</label>

        <div class="row justify-content-start px-3">
            <div class="btn-demo mr-2">
                <a href="{{route('domains.create')}}" class="btn btn-primary btn-block mg-b-10">Add domain</a>
            </div><!-- btn-demo -->
            <div class="btn-demo mr-2">
                <a href="{{route('domainsToIgnore')}}" class="btn btn-warning btn-block mg-b-10">Domains to ignore</a>
            </div><!-- btn-demo -->
            <div class="btn-demo mr-2">
                <a href="{{route('domains.to.check')}}" class="btn btn-indigo btn-block mg-b-10">Check domains</a>
            </div><!-- btn-demo -->
            <div class="btn-demo mr-2">
                <a href="{{route('domains.to.free')}}" class="btn btn-danger btn-block mg-b-10">Free</a>
            </div><!-- btn-demo -->

        </div>
        <div class="mb-2">
            <span>Current server time</span>
            <span class="text-dark font-weight-bold">
                {{\Carbon\Carbon::parse()->format('d.m.y H:i')}}
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-striped mg-b-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain name</th>
                    {{--                    <th>Total links</th>--}}
                    {{--                    <th>Parsed links</th>--}}
                    <th>Last parsed link</th>
                    <th>Last update</th>
                    <th>Free domains</th>
                    <th style="text-align: right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($domains as $key => $domain)
                    <tr>
                        <th scope="row">{{$key}}</th>
                        <td>
                            <a href="https://app.ahrefs.com/site-explorer/overview/v2/subdomains/live?target={{$domain->domain}}">{{$domain->domain}}</a>
                        </td>
                        <td>{{\Carbon\Carbon::parse($domain->last_parsed)->format('d.m.y H:i')}}</td>
                        <td>{{\Carbon\Carbon::parse($domain->last_update)->format('d.m.y H:i')}}</td>
                        {{--                        <td>{{$domain->TotalLinksCount()}}</td>--}}
                        {{--                        <td>{{$domain->parsedLinksCount()}}</td>--}}
                        <td>{{$domain->avalaibleAndVefiriedDomains()}}</td>
                        <td align="right">
                            <a href="{{route('priority',$domain->domain_id)}}" class="btn btn-success mr-1">Priority</a>
                            <a href="{{route('free', $domain->domain_id)}}" class="btn btn-warning mr-1">Free</a><a
                                href="{{route('details', $domain->domain_id)}}"
                                class="btn btn-primary mr-1">Avalible</a><a
                                data-href="{{route('domain.delete', $domain->domain_id)}}"
                                class="btn btn-danger delDomain text-white">Delete</a>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div><!-- bd -->
    </div><!-- section-wrapper -->

    @section('scripts')
        <script>
            $(document).on('click', '.delDomain', function () {
                let sureRemove = confirm('Do you really wanna remove ?');
                if (sureRemove) {
                    let url = $(this).attr('data-href');
                    let $this = $(this);
                    $.ajax({
                        type: 'POST',
                        url: url,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                        success: function (data) {
                            $this.closest('tr').remove();
                        }
                    });
                }
            })
        </script>
    @endsection
@endsection
