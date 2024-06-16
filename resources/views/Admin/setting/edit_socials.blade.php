@extends('admin/layouts/app')
@section('title', 'Edit Socials')
@section('css')
@endsection

@section('content')
<div class="row">
    <div class="col-11 p-3 m-3">
        <h3>Edit Socials</h3>

        <form action="{{route('admin.setting.update_socials' , ['id' => $data['row']->id])}}" method="POST" enctype="multipart/form-data" class="card p-2">
            @csrf
            {{method_field('PUT')}}
            <div class="mt-2 mb-2">
                <label for="social_profile_fb" class="form-label font-weight-bold"><i class="fa-brands fa-facebook"></i> Facebook</label>
                <input type="text" class="form-control" name="social_profile_fb" id="social_profile_fb" placeholder="Enter Facebook Link" value="{{$data['row']->social_profile_fb}}">
            </div>
            <div class="mt-2 mb-2">
                <label for="social_profile_insta" class="form-label font-weight-bold"><i class="fa-brands fa-instagram"></i> Instagram</label>
                <input type="text" class="form-control" name="social_profile_insta" id="social_profile_insta" placeholder="Enter Instagram Link" value="{{$data['row']->social_profile_insta}}">
            </div>
            <div class="mt-2 mb-2">
                <label for="social_profile_twitter" class="form-label font-weight-bold"><i class="fa-brands fa-twitter"></i> Twitter</label>
                <input type="text" class="form-control" name="social_profile_twitter" id="social_profile_twitter" placeholder="Enter Twitter Link" value="{{$data['row']->social_profile_twitter}}">
            </div>
            <div class="mt-2 mb-2">
                <label for="social_profile_youtube" class="form-label font-weight-bold"><i class="fa-brands fa-youtube"></i> Youtube</label>
                <input type="text" class="form-control" name="social_profile_youtube" id="social_profile_youtube" placeholder="Enter Youtube Link" value="{{$data['row']->social_profile_youtube}}">
            </div>
            <div class="mt-2 mb-2">
                <label for="social_profile_linkedin" class="form-label font-weight-bold"><i class="fa-brands fa-linkedin"></i> Linkedin</label>
                <input type="text" class="form-control" name="social_profile_linkedin" id="social_profile_linkedin" placeholder="Enter Linkedin Link" value="{{$data['row']->social_profile_linkedin}}">
            </div>
            <hr>
            <div>
                <a href="{{ route('admin.setting.index') }}" class="btn btn-sm btn-success"><i class="fa fa-ban" aria-hidden="true"></i> CANCEL</a>
                <button type="submit" class="btn btn-sm btn-warning"><i class="fa-solid fa-pen-nib" aria-hidden="true"></i> UPDATE</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('js')
@endsection