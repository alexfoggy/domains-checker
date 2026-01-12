@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <label class="section-title">Domains to ignore</label>

        <div class="row justify-content-between align-items-center">
            <div class="col-1"> <a href="{{route('domains')}}" class="btn btn-primary btn-block mg-b-10">Back</a></div>
            <div class="col-sm-6 col-md-4">
                <form action="{{route('domainsToIgnore.create')}}" method="POST">
                    @csrf
                    <div class="row justify-content-end">
                            <div class="form-group">
                                <label class="form-control-label">Domain to ignore: <span class="tx-danger">*</span></label>
                                <div class="d-flex align-items-center">
                                <input class="form-control" type="text" name="domain" value="{{old('domain')}}"
                                       placeholder="Enter domain">
                                <button type="submit" class="btn btn-primary ml-4">Add</button>
                                </div>
                        </div><!-- col-4 -->
                    </div>
                </form>
            </div><!-- col-sm-3 -->

        </div>
        <div class="table-responsive">
            <table class="table table-striped mg-b-0">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>Domain</th>
                    <th class="text-right">Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($domains as $key => $domain)
                    <tr>
                        <th scope="row">{{$key}}</th>
                        <td>{{$domain->domain}}</td>
                        <td class="text-right"><a class="btn-danger btn text-white" href="{{url('/domainstoignore/delete/'.$domain->id)}}">Delete</a></td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div><!-- bd -->
    </div><!-- section-wrapper -->
@endsection
