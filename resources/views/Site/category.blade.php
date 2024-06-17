@extends('site.layouts.app')
@section('title', 'Category')

@section('css')

@endsection
@section('content')
<section>
    <div class="container">
        <div class="row">
            <div class="col-md-9" data-aos="fade-up">
                <h3 class="category-title">Category: {{$data['category']->name}}</h3>

                @if(isset($data['post']) && $data['post']->isNotEmpty())
                @foreach($data['post'] as $row)
                <div class="d-md-flex post-entry-2 half">
                    <a href="{{ route('site.single_post', $row->slug)}}" class="me-4 thumbnail">
                        <img src="{{ asset('/uploads/post/' . $row->thumbnail) }}" alt="" class="img-fluid">
                    </a>
                    <div>
                        <div class="post-meta"><span class="date">{{$row->category->name}}</span> <span class="mx-1">&bullet;</span> <span>{{$row->created_at->format('Y-m-d')}}</span></div>
                        <h3><a href="{{ route('site.single_post', $row->slug)}}">{{$row->title}}</a></h3>
                        <p>{!! html_entity_decode(substr(($row->description), 0, 250)) !!}.........</p>
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
                <!-- Paging -->
                <div class="text-start py-4">
                    {{ $data['post']->links() }} <!-- Use $data['post'] instead of $results -->
                </div><!-- End Paging -->
                @else
                <div>
                    <h3>&nbsp;&nbsp;No Blogs To Show <i class="fa-solid fa-face-meh"></i></h3>
                    <img src="{{asset('assets/Site/oops.gif')}}" alt="">
                </div>
                @endif
            </div>
            @include('site.includes.sidebar')
        </div>
    </div>
</section>
@endsection
@section('js')

@endsection