@extends('site.layouts.app')
@section('title', 'Home')

@section('css')
<style>
    .nav-link {
        color: black;
    }
</style>
@endsection
@section('content')
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <!-- Switchable tabs -->
                <ul class="nav nav-tabs" id="myTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="for-you-tab" data-bs-toggle="tab" data-bs-target="#for-you" type="button" role="tab" aria-controls="for-you" aria-selected="true" style="background-color : "><strong>For You</strong></button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="following-tab" data-bs-toggle="tab" data-bs-target="#following" type="button" role="tab" aria-controls="following" aria-selected="false" style="background-color : "><strong>Following</strong></button>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content" id="myTabContent">
                    <!-- For You tab -->
                    <div class="tab-pane mt-2 fade show active" id="for-you" role="tabpanel" aria-labelledby="for-you-tab">
                        @if(isset($data['allPosts']) && $data['allPosts']->isNotEmpty())
                        @foreach($data['allPosts'] as $row)
                        <div class="d-md-flex post-entry-2 half">
                            <a href="{{ route('site.single_post', $row->slug)}}" class="me-4 thumbnail">
                                <img src="{{ asset('/uploads/post/' . $row->thumbnail) }}" alt="" class="img-fluid">
                            </a>
                            <div>
                                <div class="post-meta"><span class="date">{{$row->category->name}}</span> <span class="mx-1">&bullet;</span>
                                    <span>{{$row->created_at->diffForHumans()}}<span class="mx-1"></span>&bullet;</span>
                                    <span><i class="fa fa-thumbs-up"></i>{{$row->likes_count}}<span class="mx-1"></span>
                                        <span><i class="fa fa-comment"></i>{{$row->comments_count}}</span>
                                </div>
                                <h3><a href="{{ route('site.single_post', $row->slug)}}">{{$row->title}}</a></h3>
                                <p>{!! html_entity_decode(substr(($row->description), 0, 100)) !!}..</p>
                                <div class="d-flex align-items-center author">
                                    @if(isset($row->user->image))
                                    <div class="photo"><img src="{{asset('/uploads/user_image/' . $row->user->image)}}" alt="" class="img-fluid"></div>
                                    @else
                                    <div class="photo"><img src="{{asset('/assets/Site/usericon.jpg')}}" alt="" class="img-fluid"></div>
                                    @endif <div class="name">
                                        <h3 class="m-0 p-0">{{$row->user->name}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div>
                            <h3>&nbsp;&nbsp;No Blogs To Show <i class="fa-solid fa-face-meh"></i></h3>
                            <img src="{{asset('assets/Site/oops.gif')}}" alt="">
                        </div>
                        @endif
                    </div>

                    <!-- Following tab -->
                    <div class="tab-pane mt-2 fade" id="following" role="tabpanel" aria-labelledby="following-tab">
                        @if(isset($data['followingPosts']) && $data['followingPosts']->isNotEmpty())
                        @foreach($data['followingPosts'] as $row)
                        <div class="d-md-flex post-entry-2 half">
                            <a href="{{ route('site.single_post', $row->slug)}}" class="me-4 thumbnail">
                                <img src="{{ asset('/uploads/post/' . $row->thumbnail) }}" alt="" class="img-fluid">
                            </a>
                            <div>
                                <div class="post-meta"><span class="date">{{$row->category->name}}</span> <span class="mx-1">&bullet;</span>
                                    <span>{{$row->created_at->diffForHumans()}}<span class="mx-1"></span>&bullet;</span>
                                    <span><i class="fa fa-thumbs-up"></i>{{$row->likes_count}}<span class="mx-1"></span>
                                        <span><i class="fa fa-comment"></i>{{$row->comments_count}}</span>
                                </div>
                                <h3><a href="{{ route('site.single_post', $row->slug)}}">{{$row->title}}</a></h3>
                                <p>{!! html_entity_decode(substr(($row->description), 0, 100)) !!}..</p>
                                <div class="d-flex align-items-center author">
                                    @if(isset($row->user->image))
                                    <div class="photo"><img src="{{asset('/uploads/user_image/' . $row->user->image)}}" alt="" class="img-fluid"></div>
                                    @else
                                    <div class="photo"><img src="{{asset('/assets/Site/usericon.jpg')}}" alt="" class="img-fluid"></div>
                                    @endif <div class="name">
                                        <h3 class="m-0 p-0">{{$row->user->name}}</h3>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @else
                        <div>
                            <h3>&nbsp;&nbsp;No Blogs To Show <i class="fa-solid fa-face-meh"></i></h3>
                            <img src="{{asset('assets/Site/oops.gif')}}" alt="">
                        </div> @endif
                    </div>
                </div>
            </div>
            @include('site.includes.sidebar')
        </div>
    </div>
</section>
@endsection
@section('js')
@endsection