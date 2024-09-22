<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>@yield('title') | Verse</title>
    <meta content="" name="description">
    <meta content="" name="keywords">
    <link rel="icon" type="image/x-icon" href="{{asset($all_view['setting']->favicon)}}">

    <script src="{{ asset('assets/Admin/js/jquery-3.7.1.min.js') }}"></script>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=EB+Garamond:wght@400;500&family=Inter:wght@400;500&family=Playfair+Display:ital,wght@0,400;0,700;1,400;1,700&display=swap"
        rel="stylesheet">

    <link href="{{asset('assets/Site/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/Site/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
    <link href="{{asset('assets/Site/vendor/swiper/swiper-bundle.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/Site/vendor/glightbox/css/glightbox.min.css')}}" rel="stylesheet">
    <link href="{{asset('assets/Site/vendor/aos/aos.css')}}" rel="stylesheet">

    <link href="{{asset('assets/Site/css/variables.css')}}" rel="stylesheet">
    <link href="{{asset('assets/Site/css/main.css')}}" rel="stylesheet">
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.4.2/css/fontawesome.min.css"
        integrity="sha384-BY+fdrpOd3gfeRvTSMT+VUZmA728cfF9Z2G42xpaRkUGu2i3DyzpTURDo5A6CaLK" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        .pagination {
            justify-content: center;
        }

        .page-item .page-link {
            color: #fff;
            background-color: #ffffff;
            border-color: #000000;
            color: #000000;
        }

        .page-item.active .page-link {
            background-color: #000000;
            border-color: #000000;
            color: #ffffff;
        }

        .page-item.disabled .page-link {
            color: #000000;
            pointer-events: none;
            cursor: not-allowed;
            background-color: #ffffff;
            border-color: #000000;
        }
    </style>
    @yield('css')
</head>

<body>

    <!-- ======= Header ======= -->
    @include('site.includes.header')
    <!-- End Header -->

    <main id="main">

        @yield('content')

    </main><!-- End #main -->

    <!-- ======= Footer ======= -->
    @include('site.includes.footer')

    <a href="#" class="scroll-top d-flex align-items-center justify-content-center"><i
            class="bi bi-arrow-up-short"></i></a>

    <!-- Vendor JS Files -->
    <script src="{{asset('assets/Site/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('assets/Site/vendor/swiper/swiper-bundle.min.js')}}"></script>
    <script src="{{asset('assets/Site/vendor/glightbox/js/glightbox.min.js')}}"></script>
    <script src="{{asset('assets/Site/vendor/aos/aos.js')}}"></script>

    <!-- Template Main JS File -->
    <script src="{{asset('assets/Site/js/main.js')}}"></script>
    <script src="https://use.fontawesome.com/releases/v6.3.0/js/all.js" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @yield('js')
</body>

</html>