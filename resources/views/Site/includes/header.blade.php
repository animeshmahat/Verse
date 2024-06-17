 <!-- ======= Header ======= -->
 <header id="header" class="header d-flex align-items-center fixed-top">
     <div class="container-fluid container-xl d-flex align-items-center justify-content-between">

         <a href="{{route('site.index')}}" class="logo d-flex align-items-center">
             <!-- Uncomment the line below if you also wish to use an image logo -->
             <img src="{{asset($all_view['setting']->logo)}}" alt="">
         </a>

         <nav id="navbar" class="navbar">
             <ul>
                 <li><a href="{{ route('site.index') }}">Home</a></li>
                 <li><a href="#">Contact Us</a></li>
                 <li class="dropdown"><a href="#"><span>Categories</span> <i class="bi bi-chevron-down dropdown-indicator"></i></a>
                     <ul>
                         @if(isset($all_view['category']) && $all_view['category']->isNotEmpty())
                         @foreach($all_view['category'] as $category)
                         @if($loop->index < 7) <li><a href="{{ route('site.category', $category->name) }}">{{ $category->name }}</a>
                 </li>
                 @endif
                 @endforeach
                 @endif
             </ul>
             </li>
             <li><a href="#">About</a></li>

             @guest
             <li><a href="{{ route('login') }}">Login</a></li>
             @else
             <li class="dropdown">
                 <a href="#"><span>{{ Auth::user()->name }}</span> <i class="bi bi-chevron-down dropdown-indicator"></i></a>
                 <ul>
                     @if(Auth::user()->role == 'superadmin')
                     <li><a href="{{ route('admin.index') }}">Dashboard</a></li>
                     @else
                     <li><a href="{{route('site.profile' , ['id' => Auth::user()->id])}}">Profile</a></li>
                     @endif
                     <li>
                         <a href="{{ route('logout') }}" onclick="event.preventDefault(); if(confirm('Are you sure you want to logout?')) { document.getElementById('logout-form').submit(); }">
                             Logout
                         </a>
                         <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                             @csrf
                         </form>
                     </li>
                 </ul>
             </li>
             @endguest
             </ul>
         </nav><!-- .navbar -->

         <div class="position-relative">
             <a href="{{$all_view['setting']->social_profile_fb}}" target="_blank" class="mx-2"><span class="bi-facebook"></span></a>
             <a href="{{$all_view['setting']->social_profile_twitter}}" target="_blank" class="mx-2"><span class="bi-twitter"></span></a>
             <a href="{{$all_view['setting']->social_profile_insta}}" target="_blank" class="mx-2"><span class="bi-instagram"></span></a>

             <a href="{{route('site.search')}}" class="mx-2 js-search-open"><span class="bi-search"></span></a>
             <i class="bi bi-list mobile-nav-toggle"></i>

             <!-- ======= Search Form ======= -->
             <div class="search-form-wrap js-search-form-wrap">
                 <form action="{{ route('site.search') }}" class="search-form" method="GET">
                     <span class="icon bi-search"></span>
                     <input type="text" id="search" name="search" placeholder="Search" class="form-control">
                     <button class="btn js-search-close" type="submit"><span class="bi-x"></span></button>
                 </form>
                 <ul id="suggestions" style="display: none;"></ul>

                 <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                 <script>
                     $(document).ready(function() {
                         $('#search').on('input', function() {
                             let query = $(this).val().trim();

                             if (query.length > 0) {
                                 $.ajax({
                                     url: '{{ route("site.autocomplete") }}',
                                     data: {
                                         search: query
                                     },
                                     success: function(data) {
                                         let suggestions = $('#suggestions');
                                         suggestions.empty();

                                         if (data.length > 0) {
                                             suggestions.show();
                                             data.forEach(function(post) {
                                                 let highlightedTitle = post.title.replace(
                                                     new RegExp("\\b" + query + "\\b", 'gi'),
                                                     (match) => `<span class="highlight">${match}</span>`
                                                 );
                                                 suggestions.append('<li data-slug="' + post.slug + '">' + highlightedTitle + '</li>');
                                             });
                                         } else {
                                             suggestions.hide();
                                         }
                                     },
                                     error: function(xhr, status, error) {
                                         console.error("AJAX Error: " + status + error);
                                     }
                                 });
                             } else {
                                 $('#suggestions').hide();
                             }
                         });

                         $(document).on('click', '#suggestions li', function() {
                             let slug = $(this).data('slug');
                             let url = '{{ route("site.single_post", ":slug") }}';
                             url = url.replace(':slug', slug);
                             window.location.href = url;
                         });
                     });
                 </script>

                 <style>
                     #suggestions {
                         border: 1px solid #ccc;
                         background: #fff;
                         list-style: none;
                         padding: 0;
                         margin: 0;
                         position: absolute;
                         width: 300px;
                         z-index: 1000;
                     }

                     #suggestions li {
                         padding: 8px;
                         cursor: pointer;
                     }

                     #suggestions li:hover {
                         background: #f0f0f0;
                     }

                     .highlight {
                         background-color: yellow;
                     }
                 </style>
             </div>

             <!-- End Search Form -->
         </div>

     </div>

 </header><!-- End Header -->