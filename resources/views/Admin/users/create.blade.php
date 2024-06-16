@extends('admin.layouts.app')
@section('title', 'Create User')
@section('css')
<style>
    .validate {
        color: red;
    }
</style>
@endsection
@section('content')
<div class="row">
    <div class="col-12">
        <h2>Add {{$_panel}}</h2>
        <hr>
        <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="mt-2 mb-2">
                <label for="name" class="form-label"><strong>Name</strong></label>
                <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}" placeholder="Enter Name">
                @error('name')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-2 mb-2">
                <label for="email" class="form-label"><strong>Email</strong></label>
                <input type="email" class="form-control" name="email" id="email" value="{{ old('email') }}" placeholder="Enter Email">
                @error('email')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-2 mb-2">
                <label for="username" class="form-label"><strong>Username</strong></label>
                <input type="text" class="form-control" name="username" id="username" value="{{ old('username') }}" placeholder="Enter Username">
                @error('username')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-2 mb-2">
                <label for="password" class="form-label"><strong>Password</strong></label>
                <input type="password" class="form-control" name="password" id="password">
                @error('password')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-2 mb-2">
                <label for="confirm_password" class="form-label"><strong>Confirm Password</strong></label>
                <input type="password" class="form-control" name="confirm-password" id="confirm_password">
                @error('password')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-2 mb-2">
                <label for="mobile" class="form-label"><strong>Phone Number</strong></label>
                <input type="tel" class="form-control" name="mobile" id="mobile" value="{{ old('mobile') }}" placeholder="Enter Mobile Number">
                @error('mobile')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="mt-2 mb-2">
                <label for="image" class="form-label"><strong>Image</strong></label>
                <input type="file" class="form-control" name="image" id="image" onchange="loadFile(event)" placeholder="Enter Image" accept="image/png, image/gif, image/jpeg">
                <img id="output" style="max-width: 300px; max-height: 180px;" />
                @error('image')
                <p class="alert alert-danger">{{ $message }}</p>
                @enderror
            </div>
            <div class="d-flex flex-row">
                <div class="mr-4">
                    <label for="status"><strong>Status</strong></label>
                </div>
                <div class="form-check form-switch">
                    <input type="checkbox" name="status" id="status" class="form-check-input" role="switch" value="1" {{ old('status') ? 'checked' : '' }}>
                </div>
            </div>
            <hr>
            <div class="mt-2">
                <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-danger mb-4"><i class="fa fa-ban" aria-hidden="true"></i> CANCEL</a>
                <button type="reset" class="btn btn-sm btn-secondary mb-4"><i class="fa fa-refresh" aria-hidden="true"></i> RESET</button>
                <button type="submit" class="btn btn-sm btn-success mb-4"><i class="fa fa-paper-plane" aria-hidden="true"></i> SUBMIT</button>
            </div>
        </form>
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
    };
</script>
@endsection