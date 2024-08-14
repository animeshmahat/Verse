@extends('site.layouts.app')
@section('title', 'Profile')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-10 mx-auto mt-5">
            <div class="card card-shadow">
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
                    <a href="{{route('site.edit', $data['row']->id)}}" class="btn btn-outline-dark">Edit Profile &nbsp;<i class="fa fa-sm fa-user"></i></a>
                    @else
                    <form id="follow-form" action="{{ auth()->user()->isFollowing($data['row']->id) ? route('site.profile.unfollow', ['id' => $data['row']->id]) : route('site.profile.follow', ['id' => $data['row']->id]) }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="button" class="btn btn-outline-dark" id="follow-button" onclick="toggleFollow()">
                            {{ auth()->user()->isFollowing($data['row']->id) ? 'Unfollow' : 'Follow' }}
                        </button>
                    </form>
                    @endif
                    @endauth
                    <hr>
                    <div style="display:flex; flex-direction:row;">
                        <a href="#" class="list-group-item mx-auto btn btn-disabled btn-dark btn-lg p-2" onclick="showModal('followers')">Followers: <strong id="follower-count">{{$data['followersCount']}}</strong></a>
                        <a href="#" class="list-group-item mx-auto btn btn-disabled btn-dark btn-lg p-2" onclick="showModal('following')">Following: <strong id="following-count">{{$data['followingCount']}}</strong></a>
                    </div>
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
            <div class="text-start py-4">
                {{ $data['post']->links() }}
            </div>
            @else
            <div>
                <h3 class="text-center mt-2">No Posts</h3>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Followers Modal -->
<div class="modal fade" id="followersModal" tabindex="-1" aria-labelledby="followersModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followersModalLabel">Followers</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="followers-list">
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Following Modal -->
<div class="modal fade" id="followingModal" tabindex="-1" aria-labelledby="followingModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="followingModalLabel">Following</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <ul class="list-group" id="following-list">
                </ul>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    function toggleFollow() {
        const form = $('#follow-form');
        const button = $('#follow-button');
        const url = form.attr('action');

        $.ajax({
            url: url,
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                if (button.text().trim() === 'Follow') {
                    button.text('Unfollow');
                    toastr.success('Successfully followed the user.');
                    form.attr('action', '{{ route("site.profile.unfollow", ["id" => $data["row"]->id]) }}');
                    $('#follower-count').text(parseInt($('#follower-count').text()) + 1);
                } else {
                    button.text('Follow');
                    toastr.success('Successfully unfollowed the user.');
                    form.attr('action', '{{ route("site.profile.follow", ["id" => $data["row"]->id]) }}');
                    $('#follower-count').text(parseInt($('#follower-count').text()) - 1);
                }
            },
            error: function(response) {
                toastr.error('Something went wrong. Please try again.');
            }
        });
    }

    function showModal(type) {
        const url = type === 'followers' ? '{{ route("site.profile.followers", ["id" => $data["row"]->id]) }}' : '{{ route("site.profile.following", ["id" => $data["row"]->id]) }}';
        const modalId = type === 'followers' ? '#followersModal' : '#followingModal';
        const listId = type === 'followers' ? '#followers-list' : '#following-list';

        $.ajax({
            url: url,
            type: 'GET',
            success: function(users) {
                $(listId).empty();
                users.forEach(user => {
                    const userItem = `<li class="list-group-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <img src="${user.image ? '/uploads/user_image/' + user.image : 'https://via.placeholder.com/40'}" alt="Profile Picture" class="rounded-circle me-2" style="width: 40px; height: 40px;">
                                    <span>${user.name}</span>
                                </div>
                                <a href="/profile/${user.id}" class="btn btn-sm btn-dark">Profile</a>
                              </li>`;
                    $(listId).append(userItem);
                });
                $(modalId).modal('show');
            },
            error: function(response) {
                toastr.error('Failed to load data. Please try again.');
            }
        });
    }
</script>
@endsection