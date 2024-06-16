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
        <h2>Add {{$_panel}}</h2>
        <div class="card mt-3 p-3">
            <form action="{{route('admin.category.store')}}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4 mt-2">
                    <label for="title" class="form-label"><strong>Category Name :</strong></label>
                    <input type="text" name="name" id="name" class="form-control" placeholder="Enter New Category Name" value="{{ old('name') }}" autofocus>
                    @error('name')
                    <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label"><strong>Category Description :</strong></label>
                    <textarea class="form-control" name="description" id="description" cols="30" rows="9" style="resize: none;" placeholder="Enter Category Description">{{old('description')}}</textarea>
                    @error('description')
                    <p class="alert alert-danger">{{ $message }}</p>
                    @enderror
                </div>
                <a href="{{ route('admin.category.index') }}" class="btn btn-sm btn-danger mb-4"><i class="fa fa-ban" aria-hidden="true"></i> CANCEL</a>
                <button type="reset" class="btn btn-sm btn-secondary mb-4"><i class="fa fa-refresh" aria-hidden="true"></i> RESET</button>
                <button type="submit" class="btn btn-sm btn-success mb-4"><i class="fa fa-paper-plane" aria-hidden="true"></i> SUBMIT</button>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script src="//cdn.ckeditor.com/4.6.2/full/ckeditor.js"></script>
<script>
    CKEDITOR.replace('description', options);
    CKEDITOR.replace('description', options);
    var options = {
        filebrowserImageBrowseUrl: '/laravel-filemanager?type=Images',
        filebrowserImageUploadUrl: '/laravel-filemanager/upload?type=Images&_token=',
        filebrowserBrowseUrl: '/laravel-filemanager?type=Files',
        filebrowserUploadUrl: '/laravel-filemanager/upload?type=Files&_token='
    };
</script>
@endsection