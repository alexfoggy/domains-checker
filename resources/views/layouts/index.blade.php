<!DOCTYPE html>
<html lang="en">
<head>
    @include('layouts.head')
</head>
<body>

<div class="slim-mainpanel">
    <div class="container mg-t-20">
            @foreach ($errors->all() as $message)
                <div class="alert alert-danger mg-b-0" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                    {{$message}}
                </div>
            @endforeach

        @yield('content')
    </div><!-- container -->
</div><!-- slim-mainpanel -->


<script src="{{asset('lib/jquery/js/jquery.js')}}"></script>
<script src="{{asset('lib/popper.js/js/popper.js')}}"></script>
<script src="{{asset('lib/bootstrap/js/bootstrap.js')}}"></script>
<script src="{{asset('js/slim.js')}}"></script>
@yield('scripts')

</body>
</html>
