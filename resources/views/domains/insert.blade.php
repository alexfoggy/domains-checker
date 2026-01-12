@extends('layouts.index')

@section('content')
    <div class="section-wrapper mg-t-20">
        <label class="section-title">Add domain name for parsing</label>
        <div class="form-layout">
            <form action="{{route('domains.insert')}}" method="POST">
                @csrf
                <div class="row mg-b-25">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-control-label">Domain: <span class="tx-danger">*</span></label>
                            <input class="form-control" type="text" name="domain" value="{{old('domain')}}"
                                   placeholder="Enter domain">
                        </div>
                    </div><!-- col-4 -->
                </div>
                <div class="row mg-b-25">
                    <div class="col-lg-4">
                        <div class="form-group">
                            <label class="form-control-label">Domain name to ignore (if site https://nytimes.com then
                                write <b>nytimes</b>>) <span class="tx-danger">*</span></label>
                            <input class="form-control" type="text" name="same_site" value="{{old('domain')}}"
                                   placeholder="Enter domain to ignore name">
                        </div>
                    </div><!-- col-4 -->
                </div>

                <div class="rowform-layout-footer">
                    <button type="submit" class="btn btn-primary bd-0">Submit Form</button>
                </div><!-- form-layout-footer -->
            </form>
        </div><!-- form-layout -->
    </div><!-- section-wrapper -->
@endsection
