@extends('site.layouts.app')
@section('title', 'Profile')

@section('css')
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-9 mx-auto">
            <div class="card">
                @if(isset($data['row']))
                <div class="card-body text-center">
                    @if($data['row']->image)
                    <img src="{{asset('uploads/user_image/' . $data['row']->image)}}" class="img-fluid rounded-circle mb-3" alt="Profile Image" style="max-width:150px;">
                    @else
                    <img src="https://via.placeholder.com/150" class="img-fluid rounded-circle mb-3" alt="Profile Image">
                    @endif
                    <h4 class="card-title">{{$data['row']->name}}</h4>
                    @auth
                    @if(auth()->user()->id === $data['row']->id)
                    <a href="{{route('site.edit', $data['row']->id)}}" class="btn btn-outline-primary">Edit Profile</a>
                    @endif
                    @endauth
                    <hr>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Followers: 100</li>
                        <li class="list-group-item">Following: 50</li>
                        <li class="list-group-item">Posts: {{$data['post']->count()}}</li>
                    </ul>
                </div>
                @endif
            </div>

            @if(isset($data['post']) && $data['post']->isNotEmpty())
            @foreach($data['post'] as $row)
            <div class="d-md-flex post-entry-2 half mt-2">
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
                <h3 class="text-center mt-2">No Posts</h3>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection