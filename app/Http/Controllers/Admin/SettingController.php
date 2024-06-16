<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;

class SettingController extends BaseController
{
    protected $base_route = 'admin.setting.index';
    protected $view_path = 'admin.setting';
    protected $panel = 'Settings';
    protected $model;
    public function __construct()
    {
        $this->model = new Setting;
    }
    public function index()
    {
        $data = [];
        $data['row'] = DB::table('settings')->get();
        return view(parent::loadDefaultDataToView($this->view_path . '.index'), compact('data'));
    }
    public function edit($id)
    {
        $data = [];
        $data['row'] = $this->model->findOrFail($id);
        return view(parent::loadDefaultDataToView($this->view_path . '.edit'), compact('data'));
    }
    public function update(Request $request, $id)
    {
        $data = $this->model::findOrFail($id);
        $data->site_name                    = $request->site_name;
        $data->site_email                   = $request->site_email;
        $data->site_phone                   = $request->site_phone;
        $data->site_mobile                  = $request->site_mobile;
        $data->site_address                 = $request->site_address;
        $data->site_description             = $request->site_description;
        $data->meta_description             = $request->meta_description;
        $data->meta_keywords                = $request->meta_keywords;
        $data->site_url                     = $request->site_url;
        $success = $data->save();

        if ($success) {
            $request->session()->flash('update_success', $this->panel . ' successfully updated.');
            return redirect()->route($this->base_route);
        } else {
            return redirect()->route($this->base_route);
        }
    }
    public function edit_site_image($id)
    {
        $data = [];
        $data['row'] = $this->model->findorFail($id);
        return view(parent::loadDefaultDataToView($this->view_path . '.edit_site_image'), compact('data'));
    }
    public function update_site_image(Request $request, $id)
    {
        $image = $this->model::findOrFail($id);

        if ($request->hasFile('logo')) {
            if ($image->logo) {
                $logo_path = public_path($image->logo);
                if (file_exists($logo_path)) {
                    unlink($logo_path);
                }
            }

            $logo = $request->file('logo');
            $logoName = time() . '.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/site/logo'), $logoName);
            $image->logo = 'uploads/site/logo/' . $logoName;
        }
        if ($request->hasFile('favicon')) {
            if ($image->favicon) {
                $favicon_path = public_path($image->favicon);
                if (file_exists($favicon_path)) {
                    unlink($favicon_path);
                }
            }

            $favicon = $request->file('favicon');
            $faviconName = time() . '.' . $favicon->getClientOriginalExtension();
            $favicon->move(public_path('uploads/site/favicon'), $faviconName);
            $image->favicon = 'uploads/site/favicon/' . $faviconName;
        }
        $success = $image->save();

        if ($success) {
            $request->session()->flash('update_success', $this->panel . ' successfully updated.');
            return redirect()->route($this->base_route);
        } else {
            return redirect()->route($this->base_route)->with('errors', 'Couldnot Update.');
        }
    }
    public function edit_socials($id)
    {
        $data = [];
        $data['row'] = $this->model->findorFail($id);
        return view(parent::loadDefaultDataToView($this->view_path . '.edit_socials'), compact('data'));
    }
    public function update_socials(Request $request, $id)
    {
        $data = $this->model::findOrFail($id);
        $data->social_profile_fb                    = $request->social_profile_fb;
        $data->social_profile_insta                 = $request->social_profile_insta;
        $data->social_profile_twitter               = $request->social_profile_twitter;
        $data->social_profile_youtube               = $request->social_profile_youtube;
        $data->social_profile_linkedin              = $request->social_profile_linkedin;

        $success = $data->save();

        if ($success) {
            $request->session()->flash('update_success', $this->panel . ' successfully updated.');
            return redirect()->route($this->base_route);
        } else {
            return redirect()->route($this->base_route);
        }
    }
}
