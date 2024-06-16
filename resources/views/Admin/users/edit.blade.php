@extends('admin.layouts.app')

@section('title', 'Edit User')
@section('css')
<style>
    .validate {
        color: red;
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col">
        <h3>Edit {{$_panel}}</h3>
        <div class="card shadow px-4">
            <form action="{{ route('admin.users.update' , ['id' => $data['row']->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                {{method_field('PUT')}}
                <div class="mt-3 mb-2">
                    <label for="name" class="form-label"><strong>Name</strong></label>
                    <input type="text" class="form-control" name="name" id="name" value="{{ $data['row']->name }}">
                    @error('name')
                    <p class="validate m-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-3 mb-2">
                    <label for="email" class="form-label"><strong>Email</strong></label>
                    <input type="email" disabled class="form-control" name="email" id="email" value="{{ $data['row']->email }}">
                </div>

                <div class="mt-3 mb-2">
                    <label for="username" class="form-label"><strong>Username</strong></label>
                    <input type="text" class="form-control" name="username" id="username" value="{{ $data['row']->username }}">
                    @error('username')
                    <p class="validate m-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-3 mb-2">
                    <label for="mobile" class="form-label"><strong>Mobile</strong></label>
                    <input type="tel" class="form-control" name="mobile" id="mobile" value="{{ $data['row']->mobile }}">
                    @error('mobile')
                    <p class="validate m-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-3 mb-2 d-flex flex-row">
                    <label for="status" class="form-label"><strong>Status</strong></label>
                    <div class="form-check form-switch ml-4">
                        <input class="form-check-input" type="checkbox" role="switch" id="status" name="status" value="1" {{ $data['row']-> status ? 'checked' : '' }}>
                    </div>
                </div>
                <hr>
                <div class="mt-4">
                    <a href="{{ route('admin.users.index') }}" class="btn btn-sm btn-success mb-4"><i class="fa fa-ban" aria-hidden="true"></i> CANCEL</a>
                    <button type="submit" class="btn btn-sm btn-warning mb-4"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> UPDATE</button>
                </div>

                <!-- @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif -->
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
@endsection