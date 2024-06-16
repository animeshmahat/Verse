<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Admin\BaseController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserProfileController extends BaseController
{
    protected $base_route = "admin.index";
    protected $view_path = "admin.profile";
    protected $panel = "Profile";
    protected $model;

    public function __construct()
    {
        $this->middleware(['auth']);
        $this->model = new User;
    }

    public function edit($id)
    {
        $data = [];
        $data['row'] = $this->model->findOrFail($id);
        return view(parent::loadDefaultDataToView($this->view_path . '.edit'), compact('data'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'                   => 'required|max:40|min:2',
            'image'                  => 'image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048',
            'username'               => ['string', 'required', 'max:255', "unique:users,username,{$id}"],
            'mobile'                 => ['required', 'max:20', 'min:7', "unique:users,mobile,{$id}"],
            'status'                 => 'boolean'
        ]);

        $data                  = $this->model->findOrFail($id);
        $data->name            = $request->name;
        $data->username        = $request->username;
        $data->mobile          = $request->mobile;
        $data->status          = $request->status ? true : false;

        if ($request->hasFile('image')) {
            if ($data->image) {
                $image_path = public_path("uploads/user_image/{$data->image}");
                if (file_exists($image_path)) {
                    unlink($image_path);
                }
            }

            $image = $request->file('image');
            $imageName = time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/user_image'), $imageName);
            $data->image = $imageName;
        }

        $success = $data->save();

        if ($success) {
            $request->session()->flash('update_success', $this->panel . ' successfuly updated.');
            return redirect()->route($this->base_route);
        } else {
            return redirect()->route($this->base_route);
        }
    }

    public function passwordChange(Request $request)
    {
        $request->validate([
            'current_password'      => 'required',
            'password'              => 'required|max:50|required_with:password_confirmation|same:password_confirmation',
            'password_confirmation' => 'max:50|min:8'
        ]);
        if (Hash::check($request->current_password, Auth::user()->password)) {
            $row = $this->model->findOrFail(Auth::user()->id);
            $row->password = Hash::make($request->password);
            $row->save();
            session()->flash('alert-success', 'Password changed successfully.');
            Auth::logout();
            return redirect()->route('login');
        } else {
            session()->flash('alert-warning', 'Password did not match.');
            return redirect()->back()->withErrors(['current_password' => 'The password is incorrect.']);
        }
    }
}
