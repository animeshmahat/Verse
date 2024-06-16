@extends('admin.layouts.app')

@section('title', 'Category')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Blog Categories</h1>

<a href="{{ route('admin.category.create') }}" class="btn btn-sm btn-success mb-2"><i class="fa fa-plus"></i> Add {{$_panel}}</a>

<!-- Hidden element to pass session data to JavaScript -->
<div id="session-data" data-success="{{ session('success') }}" data-update-success="{{ session('update_success') }}" data-delete-success="{{ session('delete_success') }}">
</div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Category Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S.N.</th>
                        <th>Name</th>
                        <th>Description</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @if(isset($data['row']) && count($data['row']) != 0)
                    @foreach($data['row'] as $key=>$row)
                    <tr>
                        <td>{{ $key+1 }}.</td>
                        <td>{{ $row->name }}</td>
                        <td>{!! html_entity_decode($row->description) !!}</td>
                        <td>
                            <a href="{{ route('admin.category.edit', ['id' => $row->id]) }}" class="btn-circle btn-warning m m-1"><i class="fa fa-pen"></i></a>
                            <a href="{{ route('admin.category.delete', ['id' => $row->id]) }}" class="btn-circle btn-danger  m-1" onclick="return confirm('Permanently delete this record?')"><i class="fa fa-trash" aria-hidden="true"></i></a>
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

<!-- Your custom script -->
@section('js')
<!-- Toastr JS -->
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