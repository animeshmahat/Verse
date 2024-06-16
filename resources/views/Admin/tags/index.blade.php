@extends('admin.layouts.app')

@section('title', 'Tags')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
@endsection

@section('content')
<!-- Page Heading -->
<h1 class="h3 mb-2 text-gray-800">Blog Tags</h1>

<!-- Add Tag Form -->
<div class="mb-2">
    <input type="text" id="new-tags" class="form-control" placeholder="Enter new tags (comma separated)">
    <button id="add-tags" class="btn btn-sm btn-success mt-2"><i class="fa fa-plus"></i> Add Tags</button>
</div>

<!-- Notifications -->
<div id="notification-area"></div>

<!-- DataTales Example -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Tags Table</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>S.N.</th>
                        <th>Tag Name</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tfoot>
                    <tr>
                        <th>S.N.</th>
                        <th>Tag Name</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
                <tbody>
                    @foreach($data['row'] as $key => $row)
                    <tr data-id="{{ $row->id }}">
                        <td>{{ $key + 1 }}.</td>
                        <td contenteditable="true" class="editable">{{ $row->name }}</td>
                        <td>
                            <button class="btn-circle btn-danger delete-tag" data-id="{{ $row->id }}"><i class="fa-solid fa-trash"></i></button>
                        </td>
                    </tr>
                    @endforeach
                    @if($data['row']->isEmpty())
                    <tr>
                        <td colspan="3" class="text-center">No records found.</td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $(document).ready(function() {
        // Set the options for Toastr
        toastr.options.timeOut = 2000; // Set the duration to 2 seconds

        $('#add-tags').on('click', function() {
            let tags = $('#new-tags').val();

            $.ajax({
                url: "{{ route('admin.tags.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: tags
                },
                success: function(response) {
                    toastr.success(response.success);
                    location.reload();
                },
                error: function(response) {
                    toastr.error('Failed to add tags.');
                }
            });
        });

        $('.editable').on('blur', function() {
            let tagId = $(this).closest('tr').data('id');
            let tagName = $(this).text();

            $.ajax({
                url: "{{ url('admin/tags/update') }}/" + tagId,
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: tagName
                },
                success: function(response) {
                    toastr.success(response.success);
                },
                error: function(response) {
                    toastr.error('Failed to update tag.');
                }
            });
        });

        $('.delete-tag').on('click', function() {
            if (!confirm('Permanently delete this record?')) {
                return;
            }

            let tagId = $(this).data('id');

            $.ajax({
                url: "{{ url('admin/tags/delete') }}/" + tagId,
                method: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function(response) {
                    toastr.success(response.success);
                    location.reload();
                },
                error: function(response) {
                    toastr.error('Failed to delete tag.');
                }
            });
        });
    });
</script>
@endsection