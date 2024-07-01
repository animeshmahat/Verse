@extends('site.layouts.app')

@section('title', 'Edit Post')

@section('css')
<style>
    .ck-editor__editable {
        min-height: 150px;
    }
</style>
@endsection

@section('content')
<section>
    <div class="container">
        <div class="row">
            <div class="col-10 mx-auto">
                <form action="{{ route('site.post.update', $data['row']->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    {{ method_field('PUT') }}

                    <!-- categories -->
                    <div class="input-group mt-2">
                        <label for="category" class="form-label"><strong>Category</strong></label>
                    </div>
                    <div class="input-group">
                        <select class="form-select" id="category" name="category_id" autofocus>
                            <option value="" selected>Select One</option>
                            @foreach($data['category'] as $row)
                            <option value="{{ $row->id }}" {{ $row->id == $data['row']->category_id ? 'selected' : '' }}>{{ $row->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @error('category_id')
                    <div class="validate mx-1 mb-4">Please select a category.</div>
                    @enderror

                    <!-- title -->
                    <div class="mb-4 mt-2">
                        <label for="title" class="form-label"><strong>Title</strong></label>
                        <input type="text" class="form-control" id="title" name="title" placeholder="Enter Post Title" value="{{ $data['row']->title }}">
                        @error('title')
                        <div class="validate m-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- description -->
                    <div class="mb-4">
                        <label for="description" class="form-label"><strong>Content</strong></label>
                        <textarea class="form-control" id="description" name="description" placeholder="Content goes here...." cols="30" rows="9" style="resize: none;">{{ $data['row']->description }}</textarea>
                        @error('description')
                        <div class="alert alert-danger m-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label for="thumbnail" class="form-label"><strong>Thumbnail</strong></label>
                        <input type="file" class="form-control" id="thumbnail" name="thumbnail" accept="image/png, image/gif, image/jpeg">
                        @error('thumbnail')
                        <div class="validate m-1">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Current image preview -->
                    <div class="mb-4 d-flex justify-content-center" style="max-width:fit-content;">
                        @if($data['row']->thumbnail)
                        <strong class="mt-auto mb-auto">Current Thumbnail</strong>
                        <div class="v1 mx-3"></div>
                        <img id="image" src="{{ asset('/uploads/post/' . $data['row']->thumbnail) }}" alt="" class="img img-responsive p-1" style="border: 1px solid #c1c1c1; border-radius:10px; max-width:250px; max-height:180px;">
                        @else
                        <p>Thumbnail Not Found!!!</p>
                        @endif
                        <div class="v1 mx-3"></div>
                    </div>

                    <!-- status -->
                    <div class="d-flex flex-row mb-4">
                        <div class="mb-2">
                            <p><strong>Status&nbsp;&nbsp;</strong></p>
                        </div>
                        <div class="form-check form-switch">
                            <input type="checkbox" id="status" name="status" role="switch" class="form-check-input" value="1" {{ $data['row']->status ? 'checked' : '' }}>
                        </div>
                    </div>

                    <!-- tags -->
                    <div class="mb-4">
                        <label for="tags"><strong>Tags</strong></label>
                        <div class="d-flex flex-wrap">
                            @foreach($data['tags'] as $tag)
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="checkbox" name="tags[]" id="tag{{ $tag->id }}" value="{{ $tag->id }}" {{ in_array($tag->id, $data['row']->tags->pluck('id')->toArray()) ? 'checked' : '' }}>
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
</section>
@endsection

@section('js')
<script>
    var loadFile = function(event) {
        var output = document.getElementById('image');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src)
        }
    };

    document.getElementById('removeThumbnail').addEventListener('click', function() {
        document.getElementById('image').src = '';
        document.getElementById('thumbnail').value = '';
    });
</script>
<script src="https://cdn.ckeditor.com/ckeditor5/34.1.0/classic/ckeditor.js"></script>
<script>
    ClassicEditor
        .create(document.querySelector('#description'), {
            ckfinder: {
                uploadUrl: '/laravel-filemanager/upload?type=Images&_token={{ csrf_token() }}',
                options: {
                    resourceType: 'Images'
                }
            }
        })
        .catch(error => {
            console.error(error);
        });
</script>
@endsection