@extends('admin.layouts.app')

@section('title', 'Comment')

@section('css')
<style>
    #thumbnail {
        width: 80px;
        height: 50px;
        object-fit: contain;
        border: 1px solid #c1c1c1;
        border-radius: 5px;
    }
</style>
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Comments</h1>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Comments Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped" id="dataTable" width="100%" cellspacing="0">
                <thead class="text-center">
                    <tr>
                        <th>S.N.</th>
                        <th>Post</th>
                        <th>Comments Count</th>
                        <th>Sentiment Count</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot class="text-center">
                    <tr>
                        <th>S.N.</th>
                        <th>Post</th>
                        <th>Comments Count</th>
                        <th>Sentiment Count</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @if(isset($data['row']) && count($data['row']) != 0)
                        @foreach($data['row'] as $key => $post)
                            <tr>
                                <td class="text-center">{{ $key + 1 }}</td>
                                <td class="my-auto">
                                    <div class="d-flex flex-row p-2">
                                        <span class="mx-2"> <img src="{{ asset('/uploads/post/' . $post->thumbnail) }}"
                                                alt="{{ $post->title }}" id="thumbnail">
                                        </span>
                                        <span><strong>{{ $post->title }}</strong><br>({{$post->user->name}})</span>
                                    </div>
                                </td>
                                <td class="text-center">{{ $post->comments_count }}</td>
                                <td class="text-center">
                                    <div class="d-flex flex-column">
                                        <span class="btn btn-sm btn-outline-success mt-2 disabled">Positive:
                                            {{ $post->positive_count }}</span>
                                        <span class="btn btn-sm btn-outline-danger mt-2 disabled">Negative:
                                            {{ $post->negative_count }}</span>
                                        <span class="btn btn-sm btn-outline-secondary mt-2 disabled">Neutral:
                                            {{ $post->neutral_count }}</span>
                                    </div>
                                </td>
                                <td class="text-center"><a href="{{ route('admin.comment.view', ['id' => $post->id]) }}"
                                        class="btn-circle btn-outline-dark m-1"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td colspan="4">No records found.</td>
                        </tr>
                    @endif
                </tbody>

            </table>
        </div>
    </div>
</div>
@endsection

<!-- Your custom script -->
@section('js')
@endsection