@extends('admin/layouts/app')
@section('title', 'Edit Logo & Favicon')
@section('css')
<style>
    .image {
        width: 120;
        height: 120px;
        object-fit: contain;
        border: 1px solid #c1c1c1;
        border-radius: 5px;
        padding: 5px;
        background-color: #c1c1c1;
    }
</style>
@endsection

@section('content')
<h3 class="m-3">Edit Site Image</h3>
<div class="row">
    <div class="col card mx-3 p-2">
        <form action="{{route('admin.setting.update_site_image', ['id' => $data['row']->id])}}" method="post"
            enctype="multipart/form-data">
            @csrf
            {{method_field('PUT')}}
            <div class="d-flex flex-row justify-content-between">
                <div class="container-fluid mt-2 mb-2">
                    <label for="Logo" class="form-label font-weight-bold">Logo</label>
                    <input type="file" class="form-control" id="logo" name="logo" onchange="loadFile(event)"
                        placeholder="Insert Site Logo" accept="image/png, image/gif, image/jpeg">
                    <strong>Preview</strong><br>
                    <img id="output" style="max-width: 200px; max-height: 120px;" />
                    <p class="font-weight-bold mt-3">Current Logo</p>
                    <img src="{{asset($data['row']->logo)}}" class="image bg-dark" alt="Site Logo">
                </div>
                <div style="border: #c1c1c1 1px solid;"></div>
                <div class="container-fluid mt-2 mb-2">
                    <label for="Favicon" class="form-label font-weight-bold">Favicon</label>
                    <input type="file" class="form-control" id="favicon" name="favicon" onchange="loadFile(event)"
                        placeholder="Insert Site Favicon" accept="image/png, image/gif, image/jpeg">
                    <strong>Preview</strong><br>
                    <img id="output2" style="max-width: 200px; max-height: 120px;" />
                    <p class="font-weight-bold mt-3">Current Favicon</p>
                    <img src="{{asset($data['row']->favicon)}}" class="image" alt="Site Favicon">
                </div>
            </div>
            <hr>
            <div>
                <a href="{{ route('admin.setting.index') }}" class="btn btn-sm btn-success"><i class="fa fa-ban"
                        aria-hidden="true"></i> CANCEL</a>
                <button type="submit" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-nib"
                        aria-hidden="true"></i> UPDATE</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    var loadFile = function (event) {
        const inputId = event.target.id;
        const outputId = inputId === 'logo' ? 'output' : 'output2';
        const output = document.getElementById(outputId);

        output.src = URL.createObjectURL(event.target.files[0]);

        output.onload = function () {
            URL.revokeObjectURL(output.src);
        };
    };
</script>

@endsection