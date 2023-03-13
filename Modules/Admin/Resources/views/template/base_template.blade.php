<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="csrf-token" content="{{ csrf_token() }}" />

    <link rel="apple-touch-icon" sizes="76x76" href="{{asset('assets/img/apple-icon.png')}}">
    <link rel="icon" type="image/png" href="{{asset('assets/img/favicon.png')}}">
    <title>
        Tupu Time
    </title>
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600,700" rel="stylesheet"/>
    <!-- Nucleo Icons -->
    <link href="{{ asset('assets/css/nucleo-icons.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet"/>
    <link href="{{ asset('assets/css/custom.css') }}" rel="stylesheet"/>
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <link href="{{ asset('assets/css/nucleo-svg.css') }}" rel="stylesheet"/>
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ asset('assets/css/dashboard.css?v=1.0.3') }}" rel="stylesheet"/>
    <link id="pagestyle" href="{{ asset('assets/css/index.css') }}" rel="stylesheet"/>

    <!-- Toastr notification css -->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/plugins/toastr/toastr.min.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/js/plugins/fileuploads/css/fileupload.css') }}">
    <!-- Plugin for sweet alert -->
    <script src="{{ asset('assets/js/plugins/sweetalert2/sweetalert2.all.js') }}"></script>

    @yield('css')
</head>

<body class="g-sidenav-show  bg-gray-100">

<!-- Navbar -->
@include('admin::template.aside')

<main class="main-content position-relative max-height-vh-100 h-100 mt-1 border-radius-lg ">
    <!-- Navbar -->
@include('admin::template.nav')

<!-- End Navbar -->
    <div class="container-fluid py-4 min-vh-100">

        <!-- Main content -->
        @yield('content')

    </div>
</main>

<!--   UI CONFIGURATION   -->
@include('admin::template.ui_conf')

<!--   Core JS Files   -->
<script src="{{ asset('assets/js/plugins/jquery/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
<script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/smooth-scrollbar.min.js') }}"></script>
<script src="{{ asset('assets/js/plugins/chartjs.min.js') }}"></script>
<script src="{{ asset('assets/js/custom.js') }}"></script>
<script src="{{ asset('assets/js/plugins/fileuploads/js/fileupload.js') }}"></script>

<!-- Counts JS -->
<script src="{{asset('assets/js/plugins/counter/jquery.waypoints.js')}}"></script>
<script src="{{asset('assets/js/plugins/counter/jquery.counterup.min.js')}}"></script>

<script>
    $('.dropify').dropify();
    var win = navigator.platform.indexOf('Win') > -1;
    if (win && document.querySelector('#sidenav-scrollbar')) {
        var options = {
            damping: '0.5'
        }
        Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
    }

    // jQuery counterUp
    $('[data-toggle="counter"]').counterUp({
        delay: 5,
        time: 500
    });
</script>
<!-- Github buttons -->
<script async defer src="https://buttons.github.io/buttons.js"></script>
<!-- Control Center for Soft Dashboard: parallax effects, scripts for the example pages etc -->
<script src="{{ asset('assets/js/dashboard.min.js?v=1.0.3') }}"></script>

<!-- Toastr notification -->
<script src="{{ asset('assets/js/plugins/toastr/toastr.min.js') }}"></script>

<script>
    @if(Session::has('message'))
    var type = "{{Session::get('alertType', 'info')}}";
    switch (type) {
        case 'info':
            toastr.info("{{Session::get('message')}}");
            break;
        case 'success':
            toastr.success("{{Session::get('message')}}");
            break;
        case 'warning':
            toastr.warning("{{Session::get('message')}}");
            break;
        case 'error':
            toastr.error("{{Session::get('message')}}");
            break;
    }
    @endif
</script>

@yield('script')
</body>

</html>
