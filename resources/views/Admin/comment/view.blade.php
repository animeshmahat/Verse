@extends('admin.layouts.app')

@section('title', 'Comment View')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/css/bootstrap.min.css">
@endsection

@section('content')
<div class="row">
    <div class="col">
        <h2>{{ $_panel }} Comments</h2>

        <!-- back-button -->
        <a href="{{ route('admin.comment.index') }}" class="btn btn-primary btn-sm mt-2 mb-4">
            <i class="fa-solid fa-backward"></i> RETURN
        </a>

        <div class="card">
            <div class="card-body">
                <h2 class="card-title">{{ $data['row']->title }}</h2>
                <hr>

                <h5>Comments</h5>
                <hr>
                @forelse($data['row']->comments as $comment)
                    <!-- Check if the comment is not a reply -->
                    @if(is_null($comment->parent_id))
                        <div class="mb-3">
                            <div class="d-flex align-items-start">
                                <!-- User profile picture -->
                                <img src="{{asset('uploads/user_image/' . $comment->user->image) }}"
                                    alt="{{ $comment->user->name }}" class="rounded-circle me-2"
                                    style="width: 30px; height: 30px; object-fit:contain;">
                                <div>
                                    <strong>{{ $comment->user->name }}:</strong>
                                    <p>{{ $comment->comment }}</p>
                                    <span class="badge rounded-pill 
                                            @if($comment->sentiment === 'positive') badge-success 
                                            @elseif($comment->sentiment === 'negative') badge-danger 
                                            @elseif($comment->sentiment === 'neutral') badge-warning 
                                            @else badge-secondary @endif">
                                        {{ ucfirst($comment->sentiment) }}
                                    </span>
                                </div>
                            </div>
                            <hr>
                            <!-- Display replies -->
                            @if($comment->replies->isNotEmpty())
                                <div class="ms-4 mt-4">
                                    @foreach($comment->replies as $reply)
                                        <div class="mb-2 d-flex align-items-start">
                                            <!-- User profile picture for reply -->
                                            <img src="{{asset('uploads/user_image/' . $reply->user->image) }}"
                                                alt="{{ $reply->user->name }}" class="rounded-circle me-2"
                                                style="width: 30px; height: 30px; object-fit:contain;">
                                            <div>
                                                <strong>{{ $reply->user->name }} (Reply):</strong>
                                                <p>{{ $reply->comment }}</p>
                                                <span class="badge rounded-pill 
                                                                @if($reply->sentiment === 'positive') badge-success 
                                                                @elseif($reply->sentiment === 'negative') badge-danger 
                                                                @elseif($reply->sentiment === 'neutral') badge-warning 
                                                                @else badge-secondary @endif">
                                                    {{ ucfirst($reply->sentiment) }}
                                                </span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                <hr>
                            @endif
                        </div>
                    @endif
                @empty
                    <p>No comments found.</p>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.1.3/js/bootstrap.bundle.min.js"></script>
@endsection