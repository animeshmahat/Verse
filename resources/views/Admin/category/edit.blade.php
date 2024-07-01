@extends('admin.layouts.app')

@section('css')
<style>
    .ck-editor__editable {
        min-height: 150px;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-12">
        <h2>Edit {{$_panel}}</h2>
        <div class="card mt-3 p-3">
            <form action="{{route('admin.category.update', ['id' => $data['row']->id] )}}" method="POST" enctype="multipart/form-data">
                @csrf
                {{ method_field('POST') }}
                <div class="mb-4 mt-2">
                    <label for="title" class="form-label"><strong>Category Name :</strong></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter New Category Name" value="{{ $data['row']->name }}" autofocus>
                    @error('name')
                    <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label"><strong>Category Description :</strong></label>
                    <textarea class="form-control" name="description" id="description" cols="30" rows="9" style="resize: none;" placeholder="Enter Category Description">{{ $data['row']->description }}</textarea>
                    @error('description')
                    <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                </div>
                <a href="{{ route('admin.category.index') }}" class="btn btn-sm btn-danger mb-4"><i class="fa fa-ban" aria-hidden="true"></i> CANCEL</a>
                <button type="submit" class="btn btn-sm btn-success mb-4"><i class="fa fa-paper-plane" aria-hidden="true"></i> UPDATE</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
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