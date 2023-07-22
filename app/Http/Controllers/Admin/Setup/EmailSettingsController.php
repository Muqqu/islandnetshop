<?php

namespace App\Http\Controllers\Admin\Setup;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Setup\EmailSettingRequest;
use App\Repositories\Interfaces\Admin\SettingInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class EmailSettingsController extends Controller
{

    private $settings;

    public function __construct(SettingInterface $settings)
    {
        $this->settings     = $settings;
    }

    public function index(){
        return view('admin.system-setup.email-settings');
    }
    public function update(EmailSettingRequest $request){
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;

        if ($this->settings->update($request)):
            Toastr::success(__('Setting Updated Successfully'));
            return redirect()->back();
        else:
            Toastr::error(__('Something went wrong, please try again.'));
            return redirect()->back();
        endif;
    }
    public function sendTestMail(Request $request)
    {
        try {
            $data['message']     = __('Email is working Perfectly!! This is test email from ').settingHelper('system_name',\App::getLocale());
            $data['subject']     = __('Test Email');
            sendMailTo($request->send_to, $data);

            return redirect()->back();
        } catch (\Exception $e) {
            Toastr::error(__('Please check your configuration'));
            return redirect()->back();
        }
    }
}
