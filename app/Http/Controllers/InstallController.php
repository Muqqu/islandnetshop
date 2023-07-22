<?php

namespace App\Http\Controllers;

use App\Http\Requests\InstallRequest;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use URL;
use Session;
use DB;

class InstallController extends Controller
{
    public function index(){
        return view('install.index');
    }
    public function getInstall(InstallRequest $request){
        if (!Hash::check(session()->get('hash_token'),$request->random_token))
        {
            return back();
        }
//        \Session::getHandler()->gc(0);
        // do not change it
        envWrite('APP_URL', URL::to('/'));
        envWrite('MIX_ASSET_URL', URL::to('/').'/public');

        ini_set('max_execution_time', 900); //900 seconds
        $host           = $request->host;
        $db_user        = $request->db_user;
        $db_name        = $request->db_name;
        $db_password    = $request->db_password;

        $first_name     = $request->first_name;
        $last_name      = $request->last_name;
        $email          = $request->email;
        $login_password = $request->password;

        $purchase_code  = $request->purchase_code;

        //check for valid database connection
        try{
            $mysqli = @new \mysqli($host, $db_user, $db_password, $db_name);
        }catch (\Exception $e){
            return redirect()->back()->with('error', __('Please input valid database information.'))->withInput($request->all());
        }
        if (mysqli_connect_errno()) {
            return redirect()->back()->with('error', __('Please input valid database information.'))->withInput($request->all());
        }
        $mysqli->close();

        // validate code

        $data['DB_HOST']        = $host;
        $data['DB_DATABASE']    = $db_name;
        $data['DB_USERNAME']    = $db_user;
        $data['DB_PASSWORD']    = $db_password;
        $verification = validate_purchase($purchase_code, $data);
        if ($verification === 'success') :
            Session::put('email', $email);
            Session::put('first_name', $first_name);
            Session::put('last_name', $last_name);
            Session::put('login_password', $login_password);
            Session::put('purchase_code', $purchase_code);
            return redirect()->route('install.finalize');
        elseif ($verification === 'connection_error'):
            return redirect()->back()->with('error', __('There is a problem to connect with SpaGreen server.Make sure you have active internet connection!'))->withInput($request->all());
        elseif ($verification === false):
            return redirect()->back()->with('error', __('Something went wrong. Please try again.'))->withInput($request->all());
        else:
            return redirect()->back()->with('error', $verification)->withInput($request->all());
        endif;


    }
    public function final(){
        DB::statement('SET FOREIGN_KEY_CHECKS = 0');
        foreach(DB::select('SHOW TABLES') as $table) {
            $table_array = get_object_vars($table);
            \Schema::drop($table_array[key($table_array)]);
        }
        \DB::unprepared(file_get_contents(base_path('public/sql/sql.sql')));

        if(file_exists(base_path('public/sql/sql.sql'))):
            unlink(base_path('public/sql/sql.sql'));
        endif;
        \Artisan::call('key:generate');
        \Artisan::call('optimize:clear');

        $user                = User::find(1);
        $user->email         = Session::get('email');
        $user->first_name    = Session::get('first_name');
        $user->last_name     = Session::get('last_name');
        $user->password      = bcrypt(Session::get('login_password'));
        $user->save();

        $code = Setting::where('title','purchase_code')->first();

        if ($code)
        {
            $code->update([
                'value' => session()->get('purchase_code'),
            ]);
        }
        else{
            Setting::create([
                'title' => 'purchase_code',
                'value' => session()->get('purchase_code'),
            ]);
        }

        envWrite('APP_INSTALLED', 'yes');

        return redirect()->route('home');
    }
}
