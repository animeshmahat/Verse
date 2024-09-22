@extends('site.layouts.app')
@section('title', 'Blog')
@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<style>
    .tab {
        overflow: hidden;
        border-bottom: 1px solid #ccc;
    }

    .tab button {
        background-color: inherit;
        float: left;
        border: none;
        outline: none;
        cursor: pointer;
        padding: 14px 16px;
        transition: 0.3s;
    }

    .tab button:hover {
        background-color: #ddd;
    }

    .tab button.active {
        background-color: #ccc;
    }

    .tabcontent {
        display: none;
        padding: 6px 12px;
        border-top: none;
    }
</style>
@endsection

@section('content')
<section class="single-post-content">
    <div class="container">
        <div class="row">
            <div class="col-md-9 post-content">
                <div class="single-post">
                    <div class="post-meta">
                        <span class="date">{{ $data['post']->category->name }}</span>
                        <span class="mx-1">&bullet;</span>
                        <span>{{ $data['post']->created_at->format('Y-m-d D') }}</span>
                        <span><i>({{ $data['readingTime'] }} minute read)</i></span>
                    </div>
                    <div class="post-meta">
                        <i class="fa fa-eye"></i>
                        <span>{{ $data['post']->views }} views</span>
                    </div>
                    @if($data['post']->created_at != $data['post']->updated_at)
                        <span style="font-weight: italic; color: gray;">Updated at :
                            {{ $data['post']->updated_at->format('D Y-m-d') }} at
                            {{ $data['post']->updated_at->format('H:i A') }}</span>
                        <span>
                            <button class="btn btn-sm btn-outline-dark" id="summarizeBtn"
                                style="font-family:'Times New Roman', Times, serif;">SUMMARIZE</button>
                        </span>
                    @endif
                    <h1 class="mb-5">{{ $data['post']->title }}</h1>

                    <figure class="my-4">
                        <img src="{{ asset('/uploads/post/' . $data['post']->thumbnail) }}" alt="" class="img-fluid">
                    </figure>
                    <p>{!! html_entity_decode($data['post']->description) !!}</p>
                    <div class="post-meta">Posted by <a
                            href="{{route('site.profile', $data['post']->user->id)}}">{{ $data['post']->user->name }}
                            ({{ $data['post']->user->username }})</a></div>
                </div>

                @auth
                    <button id="likeButton" class="btn btn-outline-dark btn-sm">
                        <i id="likeIcon"
                            class="fa {{ $data['post']->hasLiked(Auth::user()->id) ? 'fa-thumbs-down' : 'fa-thumbs-up' }}"></i>
                        <span id="likeText">{{ $data['post']->hasLiked(Auth::user()->id) ? 'Unlike' : 'Like' }}</span>
                    </button>
                    @if(session('success'))
                        <script>
                            document.getElementById('likeText').innerText = 'Unlike';
                            document.getElementById('likeIcon').classList.replace('fa-thumbs-up', 'fa-thumbs-down');
                        </script>
                    @endif

                    @if(session('error'))
                        <script>
                            document.getElementById('likeText').innerText = 'Like';
                            document.getElementById('likeIcon').classList.replace('fa-thumbs-down', 'fa-thumbs-up');
                        </script>
                    @endif
                @endauth

                <!-- Comments -->
                <div class="comments">
                    <h5 class="comment-title py-4">{{$data['post']->likes_count}} Likes &amp;
                        {{ $data['post']->comments_count }} Comments
                    </h5>
                    <!-- Comment Display Section -->
                    @foreach($data['comments'] as $comment)
                        @if(!$comment->parent_id)
                            <div class="comment d-flex mb-4">
                                <div class="flex-shrink-0">
                                    <div class="avatar avatar-sm rounded-circle">
                                        @if($comment->user->image)
                                            <img class="avatar-img" src="{{asset('uploads/user_image/' . $comment->user->image) }}"
                                                alt="" class="img-fluid">
                                        @else
                                            <img class="avatar-img" src="{{ asset('assets/Site/usericon.png') }}" alt=""
                                                class="img-fluid">
                                        @endif
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-2 ms-sm-3">
                                    <div class="comment-meta d-flex align-items-baseline">
                                        <h6 class="me-2">{{ $comment->user->name }}</h6>
                                        <span class="text-muted">{{ $comment->created_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="comment-body">
                                        {{ $comment->comment }}
                                    </div>

                                    @if($comment->replies->count() > 0)
                                        <div class="comment-replies bg-light p-3 mt-3 rounded">
                                            <h6 class="comment-replies-title mb-4 text-muted text-uppercase">
                                                {{ $comment->replies->count() }} replies
                                            </h6>
                                            @foreach($comment->replies as $reply)
                                                <div class="reply d-flex mb-4">
                                                    <div class="flex-shrink-0">
                                                        <div class="avatar avatar-sm rounded-circle">
                                                            @if($comment->user->image)
                                                                <img class="avatar-img"
                                                                    src="{{asset('uploads/user_image/' . $comment->user->image) }}" alt=""
                                                                    class="img-fluid">
                                                            @else
                                                                <img class="avatar-img" src="{{ asset('assets/Site/usericon.png') }}" alt=""
                                                                    class="img-fluid">
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-2 ms-sm-3">
                                                        <div class="reply-meta d-flex align-items-baseline">
                                                            <h6 class="mb-0 me-2">{{ $reply->user->name }}</h6>
                                                            <span class="text-muted">{{ $reply->created_at->diffForHumans() }}</span>
                                                        </div>
                                                        <div class="reply-body">
                                                            {{ $reply->comment }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                    @auth
                                        <a href="javascript:void(0);" class="reply-link" data-comment-id="{{ $comment->id }}"
                                            style="color:blue;"><i class="fa-solid fa-reply"></i> Reply</a>
                                        @if($comment->user_id == Auth::id())
                                            <form action="{{ route('site.comment.destroy', ['id' => $comment->id]) }}" method="POST"
                                                style="display:inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="button"
                                                    class="btn btn-outline-danger btn-rounded-circle btn-sm delete-comment-btn"><i
                                                        class="fa-solid fa-trash"></i> Delete</button>
                                            </form>
                                        @endif
                                        <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display:none;">
                                            <form action="{{ route('site.comment.store', ['post_id' => $data['post']->id]) }}"
                                                method="POST">
                                                @csrf
                                                <input type="hidden" name="post_id" value="{{ $data['post']->id }}">
                                                <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                                <div class="mb-3">
                                                    <label for="comment-{{ $comment->id }}" class="form-label"
                                                        style="font-weight: bold;">Reply:</label>
                                                    <textarea class="form-control" id="comment-{{ $comment->id }}" name="comment"
                                                        rows="3" placeholder="Enter Your Comment" required></textarea>
                                                </div>
                                                <button type="submit" class="btn btn-outline-success">Submit</button>
                                            </form>
                                        </div>
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger">Login to reply</a>
                                    @endauth
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>

                @auth
                    <div class="row justify-content-center mt-5">
                        <div class="col-lg-12">
                            <h5 class="comment-title">Leave a Comment</h5>
                            <form action="{{ route('site.comment.store', ['post_id' => $data['post']->id]) }}"
                                method="POST">
                                @csrf
                                <input type="hidden" name="post_id" value="{{ $data['post']->id }}">
                                <div class="row">
                                    <div class="col-12 mb-3">
                                        <textarea class="form-control" id="comment" name="comment"
                                            placeholder="Enter your comment" cols="10" rows="10"></textarea>
                                        @error('comment')
                                            <p class="text-danger">{{ $message }}</p>
                                        @enderror
                                    </div>
                                    <div class="col-12">
                                        <input type="submit" class="btn btn-success" value="Post comment">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-outline-danger">Login to leave a comment</a>
                @endauth
            </div>
            @include('site.includes.sidebar')
        </div>
    </div>
</section>
<!-- Summarization Modal -->
<div class="modal fade" id="summarizeModal" tabindex="-1" role="dialog" aria-labelledby="summarizeModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="summarizeModalLabel">Summary</h5>
            </div>
            <div class="modal-body">
                <!-- Tab links -->
                <div class="tab">
                    <button class="tablinks" onclick="openSummary(event, 'BulletPoints')" id="defaultOpen"><i
                            class="fa-solid fa-list"></i> Points</button>
                    <button class="tablinks" onclick="openSummary(event, 'Paragraph')"><i
                            class="fa-solid fa-paragraph"></i> Paragraph</button>
                </div>

                <!-- Tab content -->
                <div id="Paragraph" class="tabcontent">
                    <p id="paragraphSummary">{!! $data['paragraph_summary'] !!}</p>
                </div>

                <div id="BulletPoints" class="tabcontent">
                    <ul id="bulletPointsSummary">
                        @if(is_array($data['bullet_point_summary']) && count($data['bullet_point_summary']) > 0)
                            @foreach($data['bullet_point_summary'] as $bullet)
                                <li>{!! $bullet !!}</li>
                            @endforeach
                        @else
                            <li>No bullet points available.</li>
                        @endif
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <p>Press <span><i>Esc</i></span> to escape.</p>
            </div>
        </div>
    </div>
</div>

@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    @if(Auth::check())
        document.getElementById('likeButton').addEventListener('click', function () {
            const postId = "{{ $data['post']->id }}";
            const isLiked = "{{ $data['post']->hasLiked(Auth::user()->id) }}";
            const url = isLiked ? `/post/${postId}/unlike` : `/post/${postId}/like`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.message === 'Post liked') {
                        toastr.success('Post liked successfully');
                        document.getElementById('likeText').innerText = 'Unlike';
                        document.getElementById('likeIcon').classList.replace('fa-thumbs-up', 'fa-thumbs-down');
                        location.reload();
                    } else if (data.message === 'Post unliked') {
                        toastr.success('Post unliked successfully');
                        document.getElementById('likeText').innerText = 'Like';
                        document.getElementById('likeIcon').classList.replace('fa-thumbs-down', 'fa-thumbs-up');
                        location.reload();
                    } else {
                        toastr.error(data.message);
                    }
                })
                .catch(error => {
                    toastr.error('Something went wrong. Please try again.');
                    console.error('Error:', error);
                });
        });
    @endif

    document.querySelectorAll('.reply-link').forEach(link => {
        link.addEventListener('click', function () {
            const commentId = this.dataset.commentId;
            const replyForm = document.getElementById(`reply-form-${commentId}`);
            if (replyForm) {
                replyForm.style.display = replyForm.style.display === 'none' ? 'block' : 'none';
            }
        });
    });

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.delete-comment-btn').forEach(function (button) {
            button.addEventListener('click', function () {
                if (confirm('Are you sure you want to delete this comment?')) {
                    button.closest('form').submit();
                }
            });
        });
        document.getElementById("defaultOpen").click();
    });
</script>
<script>
    $(document).ready(function () {
        $('#summarizeBtn').click(function () {
            $('#summarizeModal').modal('show');
        });
        document.getElementById("defaultOpen").click();
    });

    function openSummary(evt, summaryType) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tabcontent");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
        }
        tablinks = document.getElementsByClassName("tablinks");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(summaryType).style.display = "block";
        evt.currentTarget.className += " active";
    }
</script>
@endsection