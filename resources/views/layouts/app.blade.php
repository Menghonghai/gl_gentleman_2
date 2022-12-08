<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="X-UA-Compatible" content="IE=7">
    <meta name="Description" content="Bootstrap Responsive Admin Web Dashboard HTML5 Template">
    <meta name="Keywords" content="gl-gentleman, glgentleman, gl_gentleman, glgantlement, glganlalment , glgentlement">
    <meta name="Author" content="កូដដាច់បាយ">

    <title>GL2</title>


    <link rel="icon" type="image/png" href="{{ asset('public/images/favicon.png') }}">
    <!-- fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:400,400i,500,500i,700,700i">
    <!-- css -->
    <link rel="stylesheet" href="{{asset('public/vendor/bootstrap/css/bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('public/vendor/owl-carousel/assets/owl.carousel.min.css')}}">
    <link rel="stylesheet" href="{{ asset('public/vendor/photoswipe/photoswipe.css') }}">
    <link rel="stylesheet" href="{{ asset('public/vendor/photoswipe/default-skin/default-skin.css') }}"> 
    <link rel="stylesheet" href="{{ asset('public/vendor/select2/css/select2.min.css') }}">
    <link rel="stylesheet" href="{{ asset('public/assets/css/style.css') }}">
    <!-- font - fontawesome -->
    <link rel="stylesheet" href="{{ asset('public/vendor/fontawesome/css/all.min.css') }}">
    <!-- font - stroyka -->
    <link rel="stylesheet" href="fonts/stroyka/stroyka.css">

    @yield('blade_css')
</head>
<body>
    <!-- site -->

    <div class="site">
        <!-- mobile site__header -->
        @include('layouts.mobile_header')
        <!-- mobile site__header / end -->

        <!-- desktop site__header -->
        @include('layouts.destop_header')
        <!-- desktop site__header / end -->
        
         <!-- container -->
         @yield('content')
         <!-- /Container -->


        <!-- site__footer -->
        @include('layouts.footer')
        <!-- site__footer / end -->


    </div>
    <!-- site / end -->

    <!-- quickview-modal -->
    <div id="quickview-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl">
            <div class="modal-content"></div>
        </div>
    </div>
    <!-- quickview-modal / end -->
    
    <!-- mobilemenu -->
    @include('layouts.mobile_menu')
    <!-- mobilemenu / end -->

    <!-- photoswipe -->
    @include('layouts.photoswip')
    <!-- photoswipe / end -->



    




    <!-- js -->
    <script src="{{asset('public/vendor/jquery/jquery.min.js')}}"></script>
    <script src="{{asset('public/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('public/vendor/owl-carousel/owl.carousel.min.js')}}"></script>
    <script src="{{ asset('public/vendor/nouislider/nouislider.min.js') }}"></script>
    <script src="{{ asset('public/vendor/photoswipe/photoswipe.min.js') }}"></script>
    <script src="{{ asset('public/vendor/photoswipe/photoswipe-ui-default.min.js')}}"></script>
    <script src="{{ asset('public/vendor/select2/js/select2.min.js') }}"></script>
    <script src="{{ asset('public/js/number.js') }}"></script>
    <script src="{{ asset('public/js/main.js') }}"></script>
    <script src="{{ asset('public/js/header.js') }}"></script>
    <script src="{{ asset('public/vendor/svg4everybody/svg4everybody.min.js') }}"></script>
    <script>
        svg4everybody();
    </script>
     
    {{-- yield use @saction --}}
    @yield('blade_scripts')


     {{-- stack use @push --}}
     @stack('page_scripts')
</body>
</html>