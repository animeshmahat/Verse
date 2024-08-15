@extends('admin.layouts.app')

@section('title', 'Blog')

@section('css')
<style>
    .img-thumbnail {
        max-width: 440px;
        max-height: 220px;
        border: 1px solid #c1c1c1;
        object-fit: cover;
        transition: transform 0.2s ease-in-out;
    }

    .img-thumbnail:hover {
        transform: scale(1.1) !important;
    }

    .v1 {
        border-left: 1px solid #c1c1c1;
        height: inherit;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <h2>{{ $_panel }} View</h2>

        <!-- information View -->
        <div class="container mt-4 p-3" style="border: 1px solid #c1c1c1; border-radius:10px;">

            <!-- thumbnail title category status featured author date -->
            <div class="d-flex flex-row p-3">

                <!-- thumbnail -->
                <img src="{{ asset('/uploads/post/' . $data['row']->thumbnail) }}" alt="thumbnail" class="img-thumbnail mt-auto mb-auto">

                <!-- vertical st.line -->
                <div class="v1 mx-4"></div>

                <!-- title category status featured -->
                <div class="d-flex flex-column">
                    <div class="mb-2">
                        <!-- title  -->
                        <p><strong>TITLE : </strong>
                        <h3 style="font-family: Impact, Haettenschweiler, 'Arial Narrow Bold', sans-serif;">{{ $data['row']->title }}</h3>
                        </p>
                        <hr>
                    </div>
                    <!-- Sentiment -->
                    <div class="mb-2">
                        <p><strong>SENTIMENT :</strong>
                            @if($data['row']->sentiment === 'positive')
                            <span class="badge rounded-pill badge-success">Positive</span>
                            @elseif($data['row']->sentiment === 'negative')
                            <span class="badge rounded-pill badge-danger">Negative</span>
                            @elseif($data['row']->sentiment === 'neutral')
                            <span class="badge rounded-pill badge-warning">Neutral</span>
                            @else
                            <span class="badge rounded-pill badge-secondary">Unknown</span>
                            @endif
                        </p>
                    </div>

                    <!-- cat visitors status featured  -->
                    <div class="d-flex flex-row">

                        <div class="d-flex flex-column">

                            <!-- Author -->
                            <div class="mb-2">
                                <p><strong>AUTHOR :</strong>&nbsp; {{ $data['row']->user->name ?? 'not found' }}</p>
                            </div>

                            <!-- category -->
                            <div class="mb-2">
                                <p><strong>CATEGORY :</strong>&nbsp; {{ $data['row']->category->name ?? 'not found' }}</p>
                            </div>

                            <!-- status -->
                            <div class="mb-2">
                                <p><strong>STATUS :</strong>
                                    @if($data['row']->status == '1')
                                    <span class="badge rounded-pill bg-success">ACTIVE</span>
                                    @elseif($data['row']->status == '0')
                                    <span class="badge rounded-pill bg-danger">INACTIVE</span>
                                    @endif
                                </p>
                            </div>
                        </div>
                        <div class="v1 mx-5"></div>
                        <!-- status, featured, date -->
                        <div class="d-flex flex-column">

                            <!-- posted at -->
                            <div class="mb-2">
                                <p><strong>POSTED AT:</strong> {{ $data['row']->created_at->format('H:i.A D-m-d-Y') }}</p>
                            </div>

                            <!-- Visitors -->
                            <div class="mb-2">
                                <p><strong>VISITORS :</strong> {{ $data['row']->visitor }}</p>
                            </div>

                            <!-- Tags -->
                            <div class="mb-2">
                                <p><strong>TAGS :</strong>
                                    @foreach($data['row']->tags as $tag)
                                    <span class="badge bg-primary">{{ $tag->name }}</span>
                                    @endforeach
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>

            <!-- description -->
            <div class="mt-2 mb-4">
                <p><strong>CONTENT :</strong></p>
                <div>{!! html_entity_decode($data['row']->description) !!}</div>
                <hr>
            </div>
        </div>

        <!-- back-button -->
        <a href="{{route('admin.post.index')}}" class="btn btn-primary btn-sm mt-2 mb-4"><i class="fa-solid fa-backward"></i> RETURN</a>

    </div>
</div>
@endsection

@section('js')
@endsection