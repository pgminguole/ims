<!DOCTYPE html>
<html class="no-js" lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="ltr">

<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> @yield('title') - {{ config('app.name') }}</title>

    <link rel="icon" href="" type="image/x-icon"> <!-- Favicon-->

    <!-- project css file  -->
    <link rel="stylesheet" href="{{ asset('plugins/fontawesome-free/css/6-all.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/style.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
</head>

<body>
<div id="timetracker-layout" class="theme-mist">

    <!-- main body area -->
    <div class="main p-2 py-3 p-xl-5 flex-column">


        <!-- Body: Body -->
        <div class="body d-flex p-0 p-md-2 p-xl-5">
            <div class="container-xxl">
                <div class="row justify-content-center g-0 border border-secondary rounded-3 mt-2 mt-md-0">
                    <div class="col-lg-8 auth-col d-flex justify-content-center align-items-center auth-h100 bg-secondary py-2 py-md-0">
                        <div class="d-flex flex-column p-2 mt-2">
{{--                            <h1 class="text-white font-extrabold login-heading text-center text-uppercase">JSG <br>Asset Magement System</h1>--}}
                                                            <img src="{{ asset('images/logo.png') }}" class="img-fluid auth-img my-4" alt="Logo">
                            @yield('content')
                        </div>
                    </div>

                    <footer class="main-footer  col-12 mt-5 d-flex justify-content-between  border-0">
                        <span>&copy; Copyright {{ date('Y') }}  <strong><span>{{ config('app.name') }}</span></strong></span>
                        <div class="">
                            <b>Powered by</b> Judicial Service ICT Department
                        </div>
                    </footer>
                </div>
            </div>
        </div> <!-- End Row -->

    </div>
</div>








</div>
</div>

<!-- Jquery Core Js -->
<script src="{{ asset('/bundles/libscripts.bundle.js') }}"></script>

<!-- Jquery Page Js -->
<script src="{{ asset('js/template.js') }}"></script>
</body>

</html>
