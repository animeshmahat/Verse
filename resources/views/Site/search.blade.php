@extends('site.layouts.app')

@section('title', 'Search Results')

@section('css')

@endsection
@section('content')

<!-- ======= Search Results ======= -->
<section id="search-result" class="search-result">
    <div class="container">
        <div class="row">
            <div class="col-md-9">
                <h3 class="category-title">Search Results</h3>
                @if($results->count()>0)
                @foreach($results as $row)
                <div class="d-md-flex post-entry-2 small-img">
                    <a href="{{route('site.single_post', $row->slug)}}" class="me-4 thumbnail">
                        <img src="{{asset('/uploads/post/' . $row->thumbnail)}}" alt="" class="img-fluid">
                    </a>
                    <div>
                        <div class="post-meta"><span class="date">{{$row->category->name}}</span> <span class="mx-1">&bullet;</span> <span>{{$row->created_at->format('M d-Y')}}</span></div>
                        <h3><a href="{{route('site.single_post', $row->slug)}}">{{$row->title}}</a></h3>
                        <p>{!! substr(html_entity_decode($row->description), 0, 50) !!}...</p>
                        <div>
                            <span><i class="fa fa-thumbs-up"></i> {{$row->likes_count}}</span>
                            <span><i class="fa fa-comment"></i> {{$row->comments_count}}</span>
                        </div>
                        <div class="d-flex align-items-center author">
                            @if(isset($row->user->image))
                            <div class="photo"><img src="{{asset('/uploads/user_image/' . $row->user->image)}}" alt="" class="img-fluid"></div>
                            @else
                            <div class="photo"><img src="{{asset('/assets/Site/usericon.jpg')}}" alt="" class="img-fluid"></div>
                            @endif
                            <div class="name">
                                <h3 class="m-0 p-0">{{$row->user->name}}</h3>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                <!-- Paging -->
                <div class="text-start py-4">
                    {{ $results->links() }}
                </div><!-- End Paging -->
                @else
                <p>No results found</p>
                @endif
            </div>
            @include('site.includes.sidebar')
        </div>
    </div>
</section> <!-- End Search Result -->


@endsection
@section('js')

@endsection