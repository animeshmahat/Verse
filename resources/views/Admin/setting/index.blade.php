@extends('admin.layouts.app')

@section('title','Site Settings')

@section('css')
<style>
    .image {
        width: 100px;
        height: 100px;
        object-fit: contain;
        background-color: red;
    }

    a.fb {
        color: #0866ff;
        text-decoration: none;
    }

    a.insta {
        color: #f401c3;
        text-decoration: none;
    }

    a.x {
        color: #1da1f2;
        text-decoration: none;
    }

    a.yt {
        color: #f72929;
        text-decoration: none;
    }

    a.linkedin {
        color: #0077b5;
        text-decoration: none;
    }

    a:hover {
        color: #000000;
        text-decoration: none;
        cursor: pointer;
    }
</style>
@endsection

@section('content')
<div class="container-fluid">
    @if(session('update_success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('update_success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @if(session()->has('error'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session()->get('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif
    @foreach($data['row'] as $site)
    <div class="row">
        <!-- first card -->
        <div class="col-sm-6 card p-2 m-2">
            <div class="d-flex flex-row justify-content-around">
                <h2 class="mt-2">Site {{$_panel}}</h2>
                <a href="{{ route('admin.setting.edit' , ['id' => $site->id]) }}" class="btn btn-sm btn-outline-danger mt-auto mb-auto"><i class="fa-solid fa-pen"></i> Edit {{$_panel}} </a>
            </div>
            <hr>
            <form action="">
                <div class="mt-2 mb-2">
                    <label for="site_name" class="form-label font-weight-bold">Name</label>
                    <input type="text" class="form-control ms-auto" value="{{$site->site_name}}" disabled>
                </div>
                <div class="mt-2 mb-2">
                    <label for="site_email" class="form-label font-weight-bold">Email</label>
                    <input type="text" class="form-control ms-auto" value="{{$site->site_email}}" disabled>
                </div>
                <div class="mt-2 mb-2">
                    <label for="site_phone" class="form-label font-weight-bold">Phone</label>
                    <input type="text" class="form-control ms-auto" value="{{$site->site_phone}}" disabled>
                </div>
                <div class="mt-2 mb-2">
                    <label for="site_mobile" class="form-label font-weight-bold">Mobile</label>
                    <input type="text" class="form-control ms-auto" value="{{$site->site_mobile}}" disabled>
                </div>
                <div class="mt-2 mb-2">
                    <label for="site_address" class="form-label font-weight-bold">First Address</label>
                    <input type="text" class="form-control ms-auto" value="{{$site->site_address}}" disabled>
                </div>
                <div class="mt-2 mb-2">
                    <label for="site_description" class="form-label font-weight-bold">Site Description</label>
                    <textarea class="form-control ms-auto" disabled style="resize: none; text-align:justify;" rows="7">{{strip_tags($site->site_description)}}</textarea>
                </div>
                <div class="mt-2 mb-2">
                    <label for="meta_description" class="form-label font-weight-bold">Meta Description</label>
                    <textarea class="form-control ms-auto" disabled style="resize: none;">{{strip_tags($site->meta_description)}}</textarea>
                </div>
                <div class="mt-2 mb-2">
                    <label for="meta_keywords" class="form-label font-weight-bold">Meta Keywords</label>
                    <input type="text" class="form-control ms-auto" value="{{$site->meta_keywords}}" disabled>
                </div>
            </form>
        </div>
        <div class="col-sm-5 d-flex flex-column justify-content-center mb-auto">
            <!-- second card -->
            <div class="card m-2">
                <div class="d-flex flex-row justify-content-around pt-2 px-2">
                    <h2 class="mt-2">Logo & Favicon</h2>
                    <a href="{{ route('admin.setting.edit_site_image', ['id' => $site->id]) }}" class="btn btn-sm btn-outline-danger mt-auto mb-auto"><i class="fa-solid fa-pen"></i> Edit</a>
                </div>
                <hr>
                <div class="d-flex flex-row justify-content-center mb-3">
                    <img src="{{ asset($site->logo) }}" alt="site logo" class="image" title="Logo">
                    <div class="mx-4" style="border: 1px solid #c1c1c1;"></div>
                    <img src="{{ asset($site->favicon) }}" alt="site favicon" class="image" title="Favicon">
                </div>
            </div>
            <!-- third card  -->
            <div class="card m-2">
                <div class="d-flex flex-row justify-content-around pt-2 px-2">
                    <h2 class="mt-2">Socials</h2>
                    <a href="{{ route('admin.setting.edit_socials', ['id' => $site->id]) }}" class="btn btn-sm btn-outline-danger mt-auto mb-auto"><i class="fa-solid fa-pen"></i> Edit</a>
                </div>
                <hr>
                <div class="d-flex flex-row justify-content-around mb-3">
                    <h1 class="p-2" title="Facebook"><a class="fb" target="_blank" href="{{$site->social_profile_fb}}"><i class="fa-brands fa-facebook"></i></a></h1>
                    <h1 class="p-2" title="Instagram"><a class="insta" target="_blank" href="{{$site->social_profile_insta}}"><i class="fa-brands fa-instagram"></i></a></h1>
                    <h1 class="p-2" title="Twitter/X"><a class="x" target="_blank" href="{{$site->social_profile_twitter}}"><i class="fa-brands fa-twitter"></i></a></h1>
                    <h1 class="p-2" title="Youtube"><a class="yt" target="_blank" href="{{$site->social_profile_youtube}}"><i class="fa-brands fa-youtube"></i></a></h1>
                    <h1 class="p-2" title="Linkedin"><a class="linkedin" target="_blank" href="{{$site->social_profile_linkedin}}"><i class="fa-brands fa-linkedin"></i></a></h1>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endsection

@section('js')
@endsection