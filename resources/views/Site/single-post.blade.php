@extends('site.layouts.app')
@section('title', 'Forge')
@section('css')
<style>
    .img-fluid {
        max-width: 50vw;
        object-fit: contain;
    }

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
            <div class="col-md-9 post-content" data-aos="fade-up">
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
                    <span style="font-weight: italic;">Updated at : {{ $data['post']->updated_at->format('D Y-m-d') }} at {{ $data['post']->updated_at->format('H:i A') }}</span>
                    @endif
                    <h1 class="mb-5">{{ $data['post']->title }}</h1>

                    <figure class="my-4">
                        <img src="{{ asset('/uploads/post/' . $data['post']->thumbnail) }}" alt="" class="img-fluid">
                    </figure>
                    <p>{!! html_entity_decode($data['post']->description) !!}</p>
                    <div class="post-meta">Posted by {{ $data['post']->user->name }} ({{ $data['post']->user->username }})</div>
                </div>

                <div class="btn btn-outline-primary btn-sm"><i class="fa fa-thumbs-up"></i> Like</div>

                <!-- Comments -->
                <div class="comments">
                    <h5 class="comment-title py-4">{{ $data['comments']->count() }} Comments</h5>
                    @foreach($data['comments'] as $comment)
                    @if(!$comment->parent_id)
                    <div class="comment d-flex mb-4">
                        <div class="flex-shrink-0">
                            <div class="avatar avatar-sm rounded-circle">
                                <img class="avatar-img" src="{{ asset('assets/Site/usericon.jpg') }}" alt="" class="img-fluid">
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
                                <h6 class="comment-replies-title mb-4 text-muted text-uppercase">{{ $comment->replies->count() }} replies</h6>
                                @foreach($comment->replies as $reply)
                                <div class="reply d-flex mb-4">
                                    <div class="flex-shrink-0">
                                        <div class="avatar avatar-sm rounded-circle">
                                            <img class="avatar-img" src="{{ asset('assets/Site/usericon.jpg') }}" alt="" class="img-fluid">
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
                            <a href="javascript:void(0);" class="reply-link" data-comment-id="{{ $comment->id }}" style="color:red;">Reply</a>
                            <div class="reply-form" id="reply-form-{{ $comment->id }}" style="display:none;">
                                <form action="{{ route('site.comment.store', ['post_id' => $data['post']->id]) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="post_id" value="{{ $data['post']->id }}">
                                    <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                                    <div class="mb-3">
                                        <label for="comment-{{ $comment->id }}" class="form-label">comment</label>
                                        <textarea class="form-control" id="comment-{{ $comment->id }}" name="comment" rows="3" placeholder="Enter Your comment" required></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </form>
                            </div>
                            @else
                            <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Login to reply</a>
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
                        <form action="{{ route('site.comment.store', ['post_id' => $data['post']->id]) }}" method="POST">
                            @csrf
                            <input type="hidden" name="post_id" value="{{ $data['post']->id }}">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label for="comment" class="form-label">comment</label>
                                    <textarea class="form-control" id="comment" name="comment" placeholder="Enter your comment" cols="10" rows="10"></textarea>
                                    @error('comment')
                                    <p class="alert alert-danger">{{ $comment }}</p>
                                    @enderror
                                </div>
                                @if ($errors->any())
                                <div class="alert alert-danger">
                                    <ul>
                                        @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                @endif
                                <div class="col-12">
                                    <input type="submit" class="btn btn-primary" value="Post comment">
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                @else
                <p class="mt-5">Please <a href="{{ route('login') }}">login</a> to leave a comment.</p>
                @endauth

            </div>
            @include('site.includes.sidebar')
        </div>
    </div>
</section>

@endsection

@section('js')
<script src="https://code.jquery.com/jquery-3.2.1.min.js"></script>
<script>
    $(document).ready(function() {
        $('#summarizeBtn').click(function() {
            $('#summarizeModal').modal('show');
        });

        $('.reply-link').click(function() {
            var commentId = $(this).data('comment-id');
            $('#reply-form-' + commentId).toggle();
        });

        document.getElementById("defaultOpen").click();
    });
</script>
@endsection