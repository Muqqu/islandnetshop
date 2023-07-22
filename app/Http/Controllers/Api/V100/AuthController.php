<?php

namespace App\Http\Controllers\Api\V100;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\ResetPasswordPostRequest;
use App\Models\RegistrationRequest;
use App\Models\User;
use App\Traits\ApiReturnFormatTrait;
use App\Traits\SmsSenderTrait;
use App\Utility\AppSettingUtility;
use Carbon\Carbon;
use Cartalyst\Sentinel\Checkpoints\NotActivatedException;
use Cartalyst\Sentinel\Checkpoints\ThrottlingException;
use Cartalyst\Sentinel\Laravel\Facades\Activation;
use Cartalyst\Sentinel\Laravel\Facades\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Sentinel;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ApiReturnFormatTrait,SmsSenderTrait;

    public function login(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'email'     => 'required|max:255',
                'password'  => 'required|min:5|max:30',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            $user = User::where('email', $request->email)->where('user_type','customer')->where('is_deleted',0)->first();

            if (blank($user)) {
                return $this->responseWithError( __('User not found'), [], 422);
            } elseif($user->is_user_banned == 1) {
                return $this->responseWithError( __('Your account has been banned'), [], 401);
            }

            $credentials = $request->only('email', 'password');

            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return $this->responseWithError(__('Invalid credentials'), [], 401);
                }
            } catch (JWTException $e) {
                return $this->responseWithError(__('Unable to login, please try again'), [], 422);

            } catch (ThrottlingException $e) {
                return $this->responseWithError(__('Suspicious activity on your ip, try after').' '. $e->getDelay() .' '.  __('seconds'), [], 422);

            } catch (NotActivatedException $e) {
                return $this->responseWithError(__('Account is not activated. Verify your account first'),[],400);

            } catch (\Exception $e) {
                return $this->responseWithError(__('Something went wrong'), [], 500);
            }

            try {
                Sentinel::authenticate($request->all());
            } catch (NotActivatedException $e) {
                return $this->responseWithError(__('Your account is not verified.Please verify your account.'),[],500);
            }

            $data['token'] = $token;
            $data['first_name']     = $user->first_name;
            $data['last_name']      = $user->last_name;
            $data['image']          = $user->profile_image;
            $data['phone']          = nullCheck($user->phone);
            $data['email']          = nullCheck($user->email);
            $data['socials']        = $user->socials == null ? [] : $user->socials;

            return $this->responseWithSuccess(__('Login Successfully'),$data,200);

        } catch (\Exception $e){
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }

    public function register(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email' => 'required|max:255|unique:users,email',
                'password' => 'required|min:5|max:30|confirmed',
            ]);
            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            $request['user_type']   = 'customer';
            $request['permissions'] = [];
            $user = Sentinel::register($request->all());
            $activation = Activation::create($user);

            sendMail($user, $activation->code, 'verify_email');
            return $this->responseWithSuccess(__('Check your mail to verify your account'),[],200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }

    public function registerByPhone(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'phone' => 'required|max:255|unique:users,phone'
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            DB::beginTransaction();

            $request['phone'] = str_replace(' ','',$request->phone);

            $req = RegistrationRequest::where('phone',$request->phone)->first();

            if ($req && Carbon::parse($req->created_at)->addMinutes(2) >= Carbon::now())
            {
                return $this->responseWithError(__('Verification Code was Already Sent'), $validator->errors(), 500);
            }

            RegistrationRequest::where('phone',$request->phone)->delete();

            $otp = rand(10000, 99999);
            if ($request->phone && addon_is_activated('otp_system')):
                $sms_templates  = AppSettingUtility::smsTemplates();
                $sms_template   = $sms_templates->where('tab_key','signup')->first();
                $sms_body       = str_replace('{otp}', $otp, @$sms_template->sms_body);
                if (!$this->send($request->phone, $sms_body, $sms_template->template_id)):
                    return response()->json([
                        'error' => __('Unable to send otp')
                    ]);
                endif;
            endif;
            $request['otp'] = $otp;
            RegistrationRequest::create($request->all());

            DB::commit();
            return $this->responseWithSuccess(__('OTP Send Successfully'),[],200);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }

    public function verifyRegistrationOTP(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            DB::beginTransaction();
            $validator = Validator::make($request->all(), [
                'phone' => 'required',
                'otp'   => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            $request['phone'] = str_replace(' ','',$request->phone);

            $row = RegistrationRequest::where('phone',$request->phone)->first();

            if (blank($row)) {
                return $this->responseWithError( __('no_requests_found'), [], 422);
            }

            if ($request->otp != $row->otp)
            {
                return $this->responseWithError( __('OTP Doesnt Match'), [], 422);
            }

            $request['first_name']  = $row->first_name;
            $request['last_name']   = $row->last_name;
            $request['password']    = '123456';
            $request['user_type']   = 'customer';
            $request['email']       = null;


            Sentinel::registerAndActivate($request->all());

            RegistrationRequest::where('phone',$request['phone'])->delete();

            $credentials = ['phone' => $request['phone'] , 'password' => $request['password']];

            try {
                if (!$token = JWTAuth::attempt($credentials)) {
                    return $this->responseWithError(__('Invalid credentials'), [], 401);
                }
            } catch (JWTException $e) {
                return $this->responseWithError(__('Unable to login, please try again'), [], 422);

            } catch (ThrottlingException $e) {
                return $this->responseWithError(__('Suspicious activity on your ip, try after').' '. $e->getDelay() .' '.  __('seconds'), [], 422);

            } catch (\Exception $e) {
                return $this->responseWithError(__('Something went wrong'), [], 500);
            }

            try {
                Sentinel::authenticate($request->all());
            } catch (NotActivatedException $e) {
                return $this->responseWithError(__('Your account is not verified.Please verify your account.'),[],500);
            }

            $user = User::where('phone', $request->phone)->where('user_type','customer')->first();

            $data['token'] = $token;
            $data['first_name']     = $user->first_name;
            $data['last_name']      = $user->last_name;
            $data['image']          = $user->profile_image;
            $data['phone']          = nullCheck($user->phone);
            $data['email']          = nullCheck($user->email);
            $data['socials']        = $user->socials;

            DB::commit();
            return $this->responseWithSuccess(__('Registration Successfully'),$data,200);

        } catch (\Exception $e){
            DB::rollBack();
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }

    public function logout(Request $request)
    {
        try {
            Sentinel::logout();
            JWTAuth::invalidate(JWTAuth::getToken());
            return $this->responseWithSuccess(__('Logout Successfully'),[],200);
        } catch (JWTException $e) {
            JWTAuth::unsetToken();
            return $this->responseWithError(__('Failed to logout'), [], 500);
        }
    }

    public function getOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone'     => 'required|max:255',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            $request['phone'] = str_replace(' ','',$request->phone);

            $user  = User::where('phone', $request['phone'])->where('user_type','customer')->first();

            if (blank($user)):
                return $this->responseWithError(__('User Not found'), [], 500);
            endif;

            if($user->status == 0):
                return $this->responseWithError(__('Your account status is inactive'), [], 500);

            elseif($user->status == 2):
                return $this->responseWithError(__('Your account is suspend'), [], 500);

            elseif(!Activation::completed($user)):
                return $this->responseWithError(__('Your account is not verified.Please verify your account.'), [], 500);
            endif;

            $sms_templates  = AppSettingUtility::smsTemplates();

            $sms_template   = $sms_templates->where('tab_key','login')->first();
            $otp            = rand(10000,99999);
            $sms_body       = str_replace('{otp}', $otp, $sms_template->sms_body);
            if (addon_is_activated('otp_system')):
                if ($this->send($request['phone'], $sms_body, $sms_template->template_id)):
                    $user->otp  = $otp;
                    $user->save();
                    return $this->responseWithSuccess(__('Otp send successfully'),[],200);
                else:
                    return $this->responseWithSuccess(__('Unable to send otp'),[],200);
                endif;
            else:
                return $this->responseWithSuccess(__('Service is unavailable'),[],200);
            endif;
        } catch (\Exception $e){
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }

    public function verifyLoginOtp(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $validator = Validator::make($request->all(), [
                'phone'     => 'required|max:255',
                'otp'       => 'required',
            ]);

            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            $request['phone'] = str_replace(' ','',$request->phone);

            $user = User::where('phone', $request->phone)->first();

            if (blank($user)) {
                return $this->responseWithError( __('User not found'), [], 422);
            }
            if($user->is_user_banned == 1) {
                return $this->responseWithError( __('Your account has been banned'), [], 401);
            }
            if ($user->status == 0):
                return $this->responseWithError(__('You Are not Activated Yet'), [], 401);
            elseif ($user->status == 2):
                return $this->responseWithError(__('Your account is suspend'), [], 401);
            endif;

            if ($user->otp != $request->otp):
                return $this->responseWithError(__('OTP did not match. Please provide correct OTP'), [], 401);
            endif;

            try {
                if (!$token = JWTAuth::fromUser($user)) {
                    return $this->responseWithError(__('Invalid credentials'), [], 401);
                }
            } catch (JWTException $e) {
                return $this->responseWithError(__('Unable to login, please try again'), [], 422);

            } catch (ThrottlingException $e) {
                return $this->responseWithError(__('Suspicious activity on your ip, try after').' '. $e->getDelay() .' '.  __('seconds'), [], 422);

            } catch (\Exception $e) {
                return $this->responseWithError(__('Something went wrong'), [], 500);
            }

            $data['token']          = $token;
            $data['first_name']     = $user->first_name;
            $data['last_name']      = $user->last_name;
            $data['image']          = $user->profile_image;
            $data['phone']          = nullCheck($user->phone);
            $data['email']          = nullCheck($user->email);
            $data['socials']        = $user->socials;

            return $this->responseWithSuccess(__('Login Successfully'),$data,200);

        } catch (\Exception $e){
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }

    public function forgotPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|exists:users,email'
        ]);

        if ($validator->fails()) {
            return $this->responseWithError($validator->messages()->get('email')[0], $validator->errors(), 422);
        }

        try {
            $user = User::whereEmail($request->email)->first();

            $remainder = Reminder::create($user);

            sendMail($user, $remainder->code, 'forgot_password', '');

            $data = [
                'email' => $request->email,
                'code'  => $remainder->code,
            ];

            return $this->responseWithSuccess(__('You have received an email for reset your password'), $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something went wrong'), [], 500);
        }
    }

    public function createNewPassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'password' => 'required|min:6|max:32|confirmed'
        ]);

        if ($validator->fails()) {
            return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
        }

        try {
            $user = User::byEmail($request->email);
            $reminder = Reminder::exists($user, $request->code);
            if ($reminder) {

                Reminder::complete($user, $request->code, $request->password);
                sendMail($user, '', 'reset_password', $request->password);

                return $this->responseWithSuccess(__('Successfully Password Changed'), [], 500);

            } else {
                return $this->responseWithError(__('Please Request Another Code'), [], 500);
            }
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something went wrong'), [], 500);
        }
    }

    public function socialLogin(Request $request)
    {
        try {
            if ($request->has('email')){
                $user       	= User::where('email', $request->email)->where('user_type','customer')->first();
            }

            if(isset($user) && $user):
                $validator = Validator::make($request->all(), [
                    'name'  => 'required|max:255',
                    'email' => 'required|max:255|email',
                ]);
            else:
                $validator = Validator::make($request->all(), [
                    'name'  => 'required|max:255',
//                    'email' => 'required|max:255|unique:users,email',
                ]);
            endif;
            if ($validator->fails()) {
                return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
            }

            if($user):
                if($user->is_user_banned == 1) :
                    return redirect()->back()->with(['error' => __('Your account is banned')]);
                elseif($user->status == 0):
                    return redirect()->back()->with(['error' => __('Your account is inactive')]);
                endif;
            else:
                $name           = $request->name;
                $credentials = [
                    'first_name'      => $name[0],
                    'last_name'       => $name[1].(@$name[2] ? ' '.$name[2] : ''),
                    'email'           => $request->email,
                    'images'          => [],
                    'password'        => 'social-login',
                    'user_type'       => 'customer',
                    'permissions'     => [],
                    'is_password_set' => 0,
                ];
                $user               = Sentinel::register($credentials);
                $activation         = Activation::create($user);
                Activation::complete($user, $activation->code);
            endif;

            try {
                Sentinel::authenticate($user);
            } catch (NotActivatedException $e) {
                return $this->responseWithError(__('Your account is not verified.Please verify your account.'),[],500);
            }

            try {
                if (!$token = JWTAuth::fromUser($user)) :
                    return $this->responseWithError(__('Invalid credentials'), [], 401);
                endif;
            } catch (JWTException $e) {
                return $this->responseWithError(__('Unable to login, please try again'), [], 422);

            } catch (ThrottlingException $e) {
                return $this->responseWithError(__('Suspicious activity on your ip, try after').' '. $e->getDelay() .' '.  __('seconds'), [], 422);

            } catch (\Exception $e) {
                return $this->responseWithError(__('Something went wrong'), [], 500);
            }
            $data['token']          = $token;
            $data['first_name']     = $user->first_name;
            $data['last_name']      = $user->last_name;
            $data['image']          = $user->profile_image;
            $data['phone']          = nullCheck($user->phone);
            $data['email']          = nullCheck($user->email);
            $data['socials']        = $user->socials;

            return $this->responseWithSuccess(__('Login Successfully'),$data,200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something went wrong'),[],500);
        }
    }
}
