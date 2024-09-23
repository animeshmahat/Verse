<div class="col-md-3">
    <!-- ======= Sidebar ======= -->
    <div class="aside-block">
        @auth
            <div class="mb-2">
                <a class="btn btn-outline-dark" href="{{route('site.write')}}">Write &nbsp;<i
                        class="fa fa-sm fa-pen-nib"></i></a>
                <a class="btn btn-outline-dark" href="{{route('site.post.index')}}">Your Posts &nbsp;<i
                        class="fa fa-sm fa-table"></i></a>
            </div>
        @endauth

        <ul class="nav nav-pills custom-tab-nav mb-4" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="pills-popular-tab" data-bs-toggle="pill"
                    data-bs-target="#pills-popular" type="button" role="tab" aria-controls="pills-popular"
                    aria-selected="true" style="background-color:#EAFEFF !important;">Popular</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="pills-trending-tab" data-bs-toggle="pill" data-bs-target="#pills-trending"
                    type="button" role="tab" aria-controls="pills-trending" aria-selected="false"
                    style="background-color:#FFEAEA !important;">Trending</button>
            </li>
        </ul>

        <div class="tab-content" id="pills-tabContent">

            <!-- Popular -->
            <div class="tab-pane fade show active" id="pills-popular" role="tabpanel"
                aria-labelledby="pills-popular-tab">
                @if(isset($data['popularPosts']))
                    @foreach($data['popularPosts'] as $popular)
                        <div class="post-entry-1 border-bottom">
                            <div class="post-meta"><span class="date">{{$popular->category->name}}</span> <span
                                    class="mx-1">&bullet;</span> <span>{{$popular->created_at->format('M d,Y')}}</span></div>
                            <h2 class="mb-2"><a href="{{ route('site.single_post', $popular->slug)}}">{{$popular->title}}</a>
                            </h2>
                            <span class="author mb-3 d-block">{{$popular->user->name}}</span>
                        </div>
                    @endforeach
                @endif
            </div> <!-- End Popular -->

            <!-- Trending -->
            <div class="tab-pane fade" id="pills-trending" role="tabpanel" aria-labelledby="pills-trending-tab">
                @if(isset($data['trendingPosts']))
                    @foreach($data['trendingPosts'] as $trending)
                        <div class="post-entry-1 border-bottom">
                            <div class="post-meta"><span class="date">{{$trending->category->name}}</span> <span
                                    class="mx-1">&bullet;</span> <span>{{$trending->created_at->format('M d,Y')}}</span></div>
                            <h2 class="mb-2"><a href="{{ route('site.single_post', $trending->slug)}}">{{$trending->title}}</a>
                            </h2>
                            <span class="author mb-3 d-block">{{$trending->user->name}}</span>
                        </div>
                    @endforeach
                @endif
            </div> <!-- End Trending -->
        </div>
    </div>

    <div class="aside-block">
        <h3 class="aside-title">Tags</h3>
        <ul class="aside-tags list-unstyled">
            @if(isset($data['tagsWithMostPosts']))
                @foreach($data['tagsWithMostPosts'] as $tags)
                    @if($loop->index < 20)
                        <li><a href="#">{{$tags->name}}</a></li>
                    @endif
                @endforeach
            @endif
        </ul>
    </div><!-- End Tags -->

</div>