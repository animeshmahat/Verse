@extends('admin.layouts.app')

@section('title', 'Comment')

@section('css')
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
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Post Title</th>
                        <th>Comments Count</th>
                        <th>Sentiment Count</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S.N.</th>
                        <th>Post Title</th>
                        <th>Comments Count</th>
                        <th>Sentiment Count</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @if(isset($data['row']) && count($data['row']) != 0)
                        @foreach($data['row'] as $key => $post)
                            <tr>
                                <td>{{ $key + 1 }}</td>
                                <td>{{ $post->title }}</td>
                                <td>{{ $post->comments_count }}</td>
                                <td>
                                    <span class="btn btn-sm btn-outline-success">Positive: {{ $post->positive_count }}</span>
                                    <span class="btn btn-sm btn-outline-danger">Negative: {{ $post->negative_count }}</span>
                                    <span class="btn btn-sm btn-outline-secondary">Neutral: {{ $post->neutral_count }}</span>
                                </td>
                                <td><a href="{{ route('admin.comment.view', ['id' => $post->id]) }}"
                                        class="btn-circle btn-primary m-1"><i class="fa fa-eye" aria-hidden="true"></i></a></td>
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