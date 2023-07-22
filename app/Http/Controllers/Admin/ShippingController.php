<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Shipping\CityRequest;
use App\Http\Requests\Admin\Shipping\StateRequest;
use App\Http\Requests\Admin\ShippingCommissionRequest;
use App\Repositories\Interfaces\Admin\SettingInterface;
use App\Repositories\Interfaces\Admin\ShippingInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ShippingController extends Controller
{
    protected $settings;
    protected $shipping;

    public function __construct(SettingInterface $settings, ShippingInterface $shipping)
    {
        $this->settings = $settings;
        $this->shipping = $shipping;
    }

    public function configuration()
    {
        return view('admin.shipping.configuration');
    }
    public function configurationSave(ShippingCommissionRequest $request)
    {
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;
        if ($this->settings->update($request)):
            Toastr::success(__('Updated Successfully'));
            return redirect()->back();
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return redirect()->back();
        endif;
    }
    public function countries(Request $request)
    {
        $countries = $this->shipping->countriesPaginate($request,get_pagination('pagination'));

        return view('admin.shipping.countries', compact('countries'));
    }
    public function countryStatusChange(Request $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        if ($this->shipping->countryStatusChange($request['data'])):
            $response['message']    = __('Updated Successfully');
            $response['title']      = __('Success');
            $response['status']     = 'success';
            return response()->json($response);
        else:
            $response['message']    = __('Something went wrong, please try again');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
    }

    public function states(Request $request)
    {
        $countries = $this->shipping->countries()->where('status', 1)->get();
        $states = $this->shipping->statesPaginate($request, get_pagination('index_form_paginate'));

        return view('admin.shipping.states', compact('states','countries'));
    }
    public function stateStore(StateRequest $request)
    {
        if($this->shipping->stateStore($request)):
            Toastr::success(__('Created Successfully'));
            return back();
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function stateEdit(Request $request, $id)
    {
        if($state=$this->shipping->getState($id)):
            $countries   = $this->shipping->countries()->where('status', 1)->get();
            $r           = $request->server('HTTP_REFERER');
            return view('admin.shipping.state-edit',compact('state','countries','r'));
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function stateUpdate (StateRequest $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        if($this->shipping->stateUpdate($request)):
            Toastr::success(__('Updated Successfully'));
            return redirect($request->r);
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function stateStatusChange(Request $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        if ($this->shipping->stateStatusChange($request['data'])):
            $response['message']    = __('Updated Successfully');
            $response['title']      = __('Success');
            $response['status']     = 'success';
            return response()->json($response);
        else:
            $response['message']    = __('Something went wrong, please try again');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
    }

    public function cities(Request $request)
    {
        $cities = $this->shipping->citiesPaginate($request, get_pagination('index_form_paginate'));
        $states = $this->shipping->states()->where('status', 1)->get();

        return view('admin.shipping.cities', compact('cities','states'));
    }
    public function cityStore(CityRequest $request)
    {
        if($this->shipping->CityStore($request)):
            Toastr::success(__('Created Successfully'));
            return back();
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function cityEdit(Request $request, $id)
    {
        if($city=$this->shipping->getCity($id)):
            $states  = $this->shipping->states()->where('status', 1)->get();
            $r       = $request->server('HTTP_REFERER');
            return view('admin.shipping.city-edit',compact('states','city','r'));
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function cityUpdate (CityRequest $request)
    {
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;
        if($this->shipping->cityUpdate($request)):
            Toastr::success(__('Updated Successfully'));
            return redirect($request->r);
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function cityStatusChange(Request $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
        if ($this->shipping->cityStatusChange($request['data'])):
            $response['message']    = __('Updated Successfully');
            $response['title']      = __('Success');
            $response['status']     = 'success';
            return response()->json($response);
        else:
            $response['message']    = __('Something went wrong, please try again');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;
    }
    public function getStateByCountryAjax(Request $request){
        $data['states'] = $this->shipping->states()->where("country_id",$request->country_id)->where('status', 1)->get(["name","id"]);
        return response()->json($data);
    }
    public function getCityByStateAjax(Request $request){
        $data['cities'] = $this->shipping->cities()->where("state_id",$request->state_id)->where('status', 1)->get(["name","id"]);

        return response()->json($data);
    }
}
