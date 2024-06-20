@extends('site.layouts.app')

@section('title', 'Create Blog')

@section('css')
<style>
    .ck-editor__editable {
        min-height: 150px;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-9 col-sm-12 mx-auto">
            <form action="{{ route('site.post.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <!-- categories -->
                <div class="mb-4 mt-2">
                    <label for="category_id" class="form-label"><strong>Category</strong></label>
                    <select class="form-select" id="category_id" name="category_id" autofocus>
                        <option value="" selected>Select One</option>
                        @foreach($data['category'] as $row)
                        <option value="{{ $row->id }}" {{ old('category_id') == $row->id ? 'selected' : '' }}>{{ $row->name }}</option>
                        @endforeach
                    </select>
                    @error('category_id')
                    <div class="alert alert-danger mx-1 mb-4">{{ $message }}</div>
                    @enderror
                </div>

                <!-- title -->
                <div class="mb-4">
                    <label for="title" class="form-label"><strong>Title</strong></label>
                    <input type="text" class="form-control" id="title" name="title" placeholder="Enter Post Title" value="{{ old('title') }}">
                    @error('title')
                    <div class="alert alert-danger m-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- thumbnail -->
                <div class="mb-4 position-relative">
                    <label for="thumbnail" class="form-label"><strong>Thumbnail</strong></label>
                    <input type="file" class="form-control" id="thumbnail" name="thumbnail" onchange="loadFile(event)" accept="image/png, image/gif, image/jpeg">
                    <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" id="removeThumbnail" style="display:none;">&times;</button>
                    <img id="output" style="max-width: 300px; max-height: 180px; margin-top:5px;" />
                    @error('thumbnail')
                    <div class="alert alert-danger m-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- description -->
                <div class="mb-4">
                    <label for="description" class="form-label"><strong>Content</strong></label>
                    <textarea class="form-control" id="description" name="description" placeholder="Content goes here...." cols="30" rows="9" style="resize: none;">{{ old('description') }}</textarea>
                    @error('description')
                    <div class="alert alert-danger m-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- status -->
                <div class="d-flex flex-row mb-4">
                    <div class="mr-4">
                        <label for="status"><strong>Status&nbsp;&nbsp;</strong> </label>
                    </div>
                    <div class="form-check form-switch">
                        <input type="checkbox" name="status" id="status" class="form-check-input" role="switch" value="1" {{ old('status') ? 'checked' : '' }}>
                    </div>
                </div>

                <!-- tags -->
                <div class="mb-4">
                    <label for="tags"><strong>Tags</strong></label>
                    <div class="d-flex flex-wrap">
                        @foreach($data['tags'] as $tag)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="checkbox" name="tags[]" id="tag{{ $tag->id }}" value="{{ $tag->id }}" {{ is_array(old('tags')) && in_array($tag->id, old('tags')) ? 'checked' : '' }}>
                            <label class="form-check-label" for="tag{{ $tag->id }}">{{ $tag->name }}</label>
                        </div>
                        @endforeach
                    </div>
                    @error('tags')
                    <div class="alert alert-danger m-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- buttons -->
                <div class="mt-2">
                    <a href="{{ route('site.post.index') }}" class="btn btn-sm btn-danger mb-4"><i class="fa fa-ban" aria-hidden="true"></i> CANCEL</a>
                    <button type="reset" class="btn btn-sm btn-secondary mb-4"><i class="fa fa-refresh" aria-hidden="true"></i> RESET</button>
                    <button type="submit" class="btn btn-sm btn-success mb-4"><i class="fa fa-paper-plane" aria-hidden="true"></i> SUBMIT</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    var loadFile = function(event) {
        var output = document.getElementById('output');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src)
        }
        document.getElementById('removeThumbnail').style.display = 'inline-block';
    };

    document.getElementById('removeThumbnail').addEventListener('click', function() {
        document.getElementById('output').src = '';
        document.getElementById('removeThumbnail').style.display = 'none';
        document.getElementById('thumbnail').value = '';
    });
</script>
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<script>
    var options = {
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
    };
    CKEDITOR.replace('description', options);
</script>
@endsection