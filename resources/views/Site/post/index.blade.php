@extends('site.layouts.app')

@section('title', 'Blogs')

@section('css')
<style>
    #thumbnail {
        width: 100px;
        height: 60px;
        object-fit: contain;
        border: 1px solid #c1c1c1;
        border-radius: 5px;
    }
</style>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection
@section('content')
<section>
    <div class="container">
        <h1 class="h3 mb-2 text-gray-800">{{Auth()->user()->name}} Blogs</h1>
        <a href="{{ route('site.write') }}" class="btn btn-outline-dark btn-sm">Write New <i class="fa fa-pen-fancy fa-sm"></i></a>
        <a href=" {{ route('site.engagement') }}" class="btn btn-outline-dark btn-sm">Engagement <i class="fa fa-chart-simple"></i></a>
        <div id="session-data" data-success="{{ session('success') }}" data-update-success="{{ session('update_success') }}" data-delete-success="{{ session('delete_success') }}">
        </div>
        <div class="table-responsive">
            <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr class="text-center">
                        <th>S.N.</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Thumbnail</th>
                        <th>Views</th>
                        <th>Engagement</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @if(isset($data['row']) && count($data['row']) != 0)
                    @foreach($data['row'] as $key => $row)
                    <tr class="text-center">
                        <td>{{ $key + 1 }}</td>
                        <td>{{ $row->title }}</td>
                        <td>{{ $row->category->name }}</td>
                        <td>
                            <img src="{{ asset('uploads/post/'.$row->thumbnail) }}" alt="No Thumbnail" id="thumbnail">
                        </td>
                        <td><i class="fa fa-eye"></i> {{ $row->views }}</td>
                        <td><i class="fa fa-thumbs-up"></i> {{$row->likes->count()}} | <i class="fa fa-comment"></i> {{$row->comments->count()}}</td>
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
            <!-- Paging -->
            <div class="text-start">
                {{ $data['row']->links() }}
            </div><!-- End Paging -->
        </div>
    </div>
</section>
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