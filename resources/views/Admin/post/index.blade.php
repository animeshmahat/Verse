@extends('admin.layouts.app')

@section('title', 'Blogs')

@section('css')
<style>
    #thumbnail {
        width: 90px;
        height: 60px;
        object-fit: contain;
        border: 1px solid #c1c1c1;
        border-radius: 5px;
    }
</style>
<!-- Toastr CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Blog Posts</h1>

<a href="{{ route('admin.post.create') }}" class="btn btn-sm btn-success mb-2"><i class="fa fa-plus"></i> Add {{$_panel}}</a>

<!-- Hidden element to pass session data to JavaScript -->
<div id="session-data" data-success="{{ session('success') }}" data-update-success="{{ session('update_success') }}" data-delete-success="{{ session('delete_success') }}">
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header">
        <h6 class="m-0 font-weight-bold text-primary">Category Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Thumbnail</th>
                        <th>Views</th>
                        <th>Posted On & By</th>
                        <th>Sentiment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S.N.</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Thumbnail</th>
                        <th>Views</th>
                        <th>Posted On & By</th>
                        <th>Sentiment</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @if(isset($data['row']) && count($data['row']) != 0)
                    @foreach($data['row'] as $key=>$row)
                    <tr>
                        <td>{{ $key+1 }}</td>
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->category->name }}</td>
                        <td>
                            <img src="{{ asset('/uploads/post/' . $row->thumbnail) }}" alt="thumbnail" id="thumbnail">
                        </td>
                        <td>{{ $row->views }}</td>
                        <td>{{ $row->created_at->format('H:i.A D-m-d-Y') }} by <br> <strong>{{$row->user->name}}</strong> ({{$row->user->username}})</td>
                        <td>#</td>
                        <td>
                            @if($row->status == '1')
                            <span class="badge rounded-pill badge-success">Active</span>
                            @elseif($row->status == '0')
                            <span class="badge rounded-pill badge-danger">InActive</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex flex-row align-items-center">
                                <a href="{{ route('admin.post.view', ['id' => $row->id]) }}" class="btn-circle btn-primary m-1"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                @if (Auth::id() === $row->user_id)
                                <a href="{{ route('admin.post.edit', ['id' => $row->id]) }}" class="btn-circle btn-warning m-1"><i class="fa fa-pen" aria-hidden="true"></i></a>
                                <a href="{{ route('admin.post.delete', ['id' => $row->id]) }}" class="btn-circle btn-danger m-1" onclick="return confirm('Permanently delete this record?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @elseif (Auth::user()->role === 'superadmin')
                                <a href="{{ route('admin.post.delete', ['id' => $row->id]) }}" class="btn-circle btn-danger m-1" onclick="return confirm('Permanently delete this record?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                    @elseif(count($data['row']) == 0)
                    <h3>No records found.</h3>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const sessionData = document.getElementById('session-data');
        const successMessage = sessionData.getAttribute('data-success');
        const updateSuccessMessage = sessionData.getAttribute('data-update-success');
        const deleteSuccessMessage = sessionData.getAttribute('data-delete-success');

        if (successMessage) {
            toastr.success(successMessage);
        }
        if (updateSuccessMessage) {
            toastr.info(updateSuccessMessage);
        }
        if (deleteSuccessMessage) {
            toastr.warning(deleteSuccessMessage);
        }
    });
</script>
@endsection