@extends('admin.layouts.app')

@section('title', 'Tags')

@section('css')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css">
<style>
    .tag-box {
        display: inline-block;
        padding: 5px 10px;
        background-color: #f0f0f0;
        border-radius: 5px;
        margin: 5px;
        cursor: pointer;
    }

    .tag-box:hover {
        background-color: #d4d4d4;
    }

    .delete-btn {
        color: red;
        margin-left: 10px;
        cursor: pointer;
    }

    .editable {
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<h1 class="h3 mb-2 text-gray-800">Blog Tags</h1>

<div class="mb-2">
    <input type="text" id="new-tags" class="form-control" placeholder="Enter new tags (comma separated)">
    <button id="add-tags" class="btn btn-sm btn-success mt-2"><i class="fa fa-plus"></i> Add Tags</button>
</div>

<div id="notification-area"></div>

<!-- Tags display as rectangular boxes -->
<div id="tag-container">
    @foreach($data['row'] as $row)
        <span class="tag-box" data-id="{{ $row->id }}">
            {{ $row->name }}
            <span class="delete-btn" data-id="{{ $row->id }}">&times;</span>
        </span>
    @endforeach
</div>

@if($data['row']->isEmpty())
    <p>No tags available.</p>
@endif
@endsection

@section('js')
<script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>
<script>
    $(document).ready(function () {
        toastr.options.timeOut = 2000; // Set the duration to 2 seconds

        // Add tags
        $('#add-tags').on('click', function () {
            let tags = $('#new-tags').val();

            $.ajax({
                url: "{{ route('admin.tags.store') }}",
                method: "POST",
                data: {
                    _token: "{{ csrf_token() }}",
                    name: tags
                },
                success: function (response) {
                    toastr.success(response.success);
                    location.reload();
                },
                error: function (response) {
                    if (response.status === 422) {
                        toastr.error(response.responseJSON.error);
                    } else {
                        toastr.error('Failed to add tags.');
                    }
                }
            });
        });

        // Edit tags (on click of the tag name)
        $('.tag-box').on('click', function () {
            let tagId = $(this).data('id');
            let tagName = prompt('Edit tag name:', $(this).text());

            if (tagName) {
                $.ajax({
                    url: "{{ url('admin/tags/update') }}/" + tagId,
                    method: "POST",
                    data: {
                        _token: "{{ csrf_token() }}",
                        name: tagName.trim()
                    },
                    success: function (response) {
                        toastr.success(response.success);
                        location.reload();
                    },
                    error: function (response) {
                        toastr.error('Failed to update tag.');
                    }
                });
            }
        });

        // Delete tags
        $('.delete-btn').on('click', function (e) {
            e.stopPropagation(); // Prevent triggering edit on delete

            if (!confirm('Permanently delete this tag?')) {
                return;
            }

            let tagId = $(this).data('id');

            $.ajax({
                url: "{{ url('admin/tags/delete') }}/" + tagId,
                method: "DELETE",
                data: {
                    _token: "{{ csrf_token() }}"
                },
                success: function (response) {
                    toastr.success(response.success);
                    location.reload();
                },
                error: function (response) {
                    toastr.error('Failed to delete tag.');
                }
            });
        });
    });
</script>
@endsection