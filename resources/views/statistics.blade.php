@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <label class="section-title">@if($domain)
                Avalaible domains from {{$domain->domain}}
            @endif</label>

        <a href="{{route('domains')}}" class="btn btn-primary btn-block mg-b-10">Back</a>
        <div class="table-responsive">
            <table class="table table-striped mg-b-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    <th style="width: 30%;">From</th>
                    <th>Date</th>
                    <th style="text-align: right">Actions</th>
                </tr>
                </thead>
                <tbody>
                @foreach($avalaibleDomains as $key => $domain)
                    <tr>

                        <th scope="row" @if($domain->status==\App\AvalaibleDomains\DomainStatus::REJECTED)
                            style="background: #dc3545"
                            @elseif($domain->status==\App\AvalaibleDomains\DomainStatus::CONFIRM)
                                style="background: #1CAF9A"
                            @endif>{{$domain->id}}</th>
                        <td><a href="https://app.ahrefs.com/site-explorer/overview/v2/subdomains/live?target={{$domain->domain}}"
                               target="_blank">{{$domain->domain}} @if($domain->is_premium)
                                    <span class="btn-warning px-2 py-1 font-weight-bold rounded"
                                          style="font-size: 10px">premium</span>
                                @endif</a></td>
                        <td style="width: 30%;"><a href="{{$domain->from}}" target="_blank">{{$domain->from}}</a></td>
                        <td>{{$domain->created_at}}</td>
                        <td align="right">
                            <div class="dropdown mg-sm-l-10 mg-t-10 mg-sm-t-0">
                                <button class="btn btn-outline-primary dropdown-toggle" type="button"
                                        id="dropdownMenuButton2" data-toggle="dropdown" aria-haspopup="true"
                                        aria-expanded="false">
                                    Status
                                </button>
                                <div class="dropdown-menu" aria-labelledby="dropdownMenuButton2">
                                    <a class="dropdown-item"
                                       href="{{route('domain.change.status', [$domain->id, \App\AvalaibleDomains\DomainStatus::UNKNOWN])}}">UNKNOWN</a>
                                    <a class="dropdown-item"
                                       href="{{route('domain.change.status', [$domain->id, \App\AvalaibleDomains\DomainStatus::CONFIRM])}}">CONFIRM</a>
                                    <a class="dropdown-item"
                                       href="{{route('domain.change.status', [$domain->id, \App\AvalaibleDomains\DomainStatus::REJECTED])}}">REJECTED</a>
                                </div><!-- dropdown-menu -->
                            </div><!-- dropdown -->
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            {{$avalaibleDomains->links()}}
        </div><!-- bd -->
    </div><!-- section-wrapper -->
@endsection
