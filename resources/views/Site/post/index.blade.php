@extends('site.layouts.app')

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
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
<div class="container">
    <h1 class="h3 mb-2 text-gray-800">{{Auth()->user()->name}} Blogs</h1>
    <div id="session-data" data-success="{{ session('success') }}" data-update-success="{{ session('update_success') }}" data-delete-success="{{ session('delete_success') }}">
    </div>

    <div class="card shadow mb-4">
        <div class="card-header">
            <h6 class="m-0 text-primary">Blog Table</h6>
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
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </tfoot>
                    <tbody>
                        @if(isset($data['row']) && count($data['row']) != 0)
                        @foreach($data['row'] as $key => $row)
                        <tr>
                            <td>{{ $key + 1 }}</td>
                            <td>{{ $row->title }}</td>
                            <td>{{ $row->category->name }}</td>
                            <td>
                                <img src="{{ asset('uploads/post/'.$row->thumbnail) }}" alt="No Thumbnail" id="thumbnail">
                            </td>
                            <td>{{ $row->views }}</td>
                            <td>
                                {{ $row->created_at }} <br>
                                <strong>By: </strong> {{ $row->user->name }}
                            </td>
                            <td>
                                @if ($row->status == '1')
                                <span class="text-success">ACTIVE</span>
                                @elseif ($row->status == '0')
                                <span class="text-danger">INACTIVE</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('site.single_post', ['slug' => $row->slug]) }}" class="btn btn-sm btn-dark" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                                <a href="{{ route('site.post.edit', $row->id) }}" class="btn btn-sm btn-warning" title="Edit">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <a href="{{ route('site.post.delete', $row->id) }}" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure you want to delete this item?');">
                                    <i class="fa fa-trash-alt"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                        @else
                        <tr>
                            <td colspan="8" class="text-center">No data available.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        const sessionData = $('#session-data').data();
        if (sessionData.success) toastr.success(sessionData.success);
        if (sessionData.update_success) toastr.success(sessionData.update_success);
        if (sessionData.delete_success) toastr.success(sessionData.delete_success);
    });
</script>
@endsection