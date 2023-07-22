<?php

namespace App\Http\Controllers\Api\V100;

use App\Http\Controllers\Controller;
use App\Repositories\Interfaces\Admin\Addon\WalletInterface;
use App\Repositories\Interfaces\UserInterface;
use App\Traits\ApiReturnFormatTrait;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class UserController extends Controller
{
    use ApiReturnFormatTrait;

    public function profile(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = null;
            if ($request->token) {
                try {
                    if (!$user = JWTAuth::parseToken()->authenticate()) {
                        return $this->responseWithError(__('unauthorized_user'), [], 401);
                    }
                } catch (\Exception $e) {
                    return $this->responseWithError(__('unauthorized_user'), [], 401);
                }
            }

            $data = [
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => nullCheck($user->email),
                'phone'             => nullCheck($user->phone),
                'gender'            => nullCheck($user->gender),
                'date_of_birth'     => nullCheck($user->date_of_birth),
                'image'             => $user->profile_image,
                'facebook'          => $user->socials && count($user->socials) > 0 && array_key_exists('facebook',$user->socials) ? $user->socials['facebook'] : '',
                'twitter'           => $user->socials && count($user->socials) > 0 && array_key_exists('twitter',$user->socials) ? $user->socials['twitter'] : '',
                'linkedin'          => $user->socials && count($user->socials) > 0 && array_key_exists('linkedin',$user->socials) ? $user->socials['linkedin'] : '',
                'instagram'         => $user->socials && count($user->socials) > 0 && array_key_exists('instagram',$user->socials) ? $user->socials['instagram'] : '',
                'pinterest'         => $user->socials && count($user->socials) > 0 && array_key_exists('pinterest',$user->socials) ? $user->socials['pinterest'] : '',
                'youtube'           => $user->socials && count($user->socials) > 0 && array_key_exists('youtube',$user->socials) ? $user->socials['youtube'] : '',
            ];
            return $this->responseWithSuccess(__('Data Retrieved Successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something Went Wrong'), [], null);
        }
    }

    public function updateProfile(Request $request,UserInterface $userInterface): \Illuminate\Http\JsonResponse
    {
        $user = null;
        if ($request->token) {
            try {
                if (!$user = JWTAuth::parseToken()->authenticate()) {
                    return $this->responseWithError(__('unauthorized_user'), [], 401);
                }
            } catch (\Exception $e) {
                return $this->responseWithError(__('unauthorized_user'), [], 401);
            }
        }
        $validator = Validator::make($request->all(), [
            'first_name'    => 'required|max:50|regex:/^[a-zA-Z\s]*$/',
            'last_name'     => 'required|max:50|regex:/^[a-zA-Z\s]*$/',
            'email'         => 'required_without:phone|max:50|email|unique:users,email,'.$user->id,
            'phone'         => 'required_without:email|min:4|max:20|unique:users,phone,'.$user->id,
            'gender'        => 'required',
            'date_of_birth' => 'required',
        ]);

        if ($validator->fails()) {
            return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
        }

        try {

            if($request->phone):
                $request['phone'] = str_replace(' ','',$request->phone);
            endif;

            $socials = [];

            if ($request->facebook)
            {
                $socials['facebook'] = $request->facebook;
            }

            if ($request->twitter)
            {
                $socials['twitter'] = $request->twitter;
            }

            if ($request->linkedin)
            {
                $socials['linkedin'] = $request->linkedin;
            }

            if ($request->instagram)
            {
                $socials['instagram'] = $request->instagram;
            }

            if ($request->pinterest)
            {
                $socials['pinterest'] = $request->pinterest;
            }

            if ($request->youtube)
            {
                $socials['youtube'] = $request->youtube;
            }

            $request['id']              = $user->id;
            $request['socials']         = $socials;
            $request['date_of_birth']   = $request->date_of_birth ? Carbon::parse($request->date_of_birth)->format('Y-m-d') : null;

            $user = $userInterface->update($request);
            $data = [
                'first_name'        => $user->first_name,
                'last_name'         => $user->last_name,
                'email'             => nullCheck($user->email),
                'phone'             => nullCheck($user->phone),
                'gender'            => nullCheck($user->gender),
                'date_of_birth'     => nullCheck($user->date_of_birth),
                'image'             => $user->profile_image,
            ];

            return $this->responseWithSuccess(__('Profile Updated Successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something Went Wrong'), [], null);
        }
    }

    public function changePassword(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'current_password'       => 'min:6|max:32',
            'new_password'           => 'required|min:6|max:32|required_with:confirm_password|same:confirm_password',
            'confirm_password'       => 'required|min:6|max:32',
        ]);

        if ($validator->fails()) {
            return $this->responseWithError(__('Required field missing'), $validator->errors(), 422);
        }

        try {
            $user = null;
            if ($request->token) {
                try {
                    if (!$user = JWTAuth::parseToken()->authenticate()) {
                        return $this->responseWithError(__('unauthorized_user'), [], 401);
                    }
                } catch (\Exception $e) {
                    return $this->responseWithError(__('unauthorized_user'), [], 401);
                }
            }

            if (Hash::check($request->new_password, $user->password)) {
                return $this->responseWithError(__('New password cannot be same as current password'), [], 200);
            }
            if(Hash::check($request->current_password, $user->password)){
                $user->password = bcrypt($request->new_password);
                $user->last_password_change = Carbon::now();
                $user->save();
                return $this->responseWithSuccess(__('Password Changed Successfully'), [], 200);

            } else{
                return $this->responseWithError(__('Current Password does not match with old password'), [], 200);
            }

        } catch (\Exception $e) {
            return $this->responseWithError(__('Something Went Wrong'), [], null);
        }
    }

    public function myWallet(WalletInterface $wallet): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'recharges' => $wallet->walletHistory(),
                'balance' => $wallet->userBalance()
            ];
            return $this->responseWithSuccess(__('Wallet Data Retrieved'), $data, 200);

        } catch (\Exception $e) {
            return $this->responseWithError(__('Something Went Wrong'), [], null);
        }
    }

    public function destroy(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $user = null;
            if ($request->token) {
                try {
                    if (!$user = JWTAuth::parseToken()->authenticate()) {
                        return $this->responseWithError(__('unauthorized_user'), [], 401);
                    }
                } catch (\Exception $e) {
                    return $this->responseWithError(__('unauthorized_user'), [], 401);
                }
            }
            if($user->is_deleted == 1)
            {
                return $this->responseWithError(__('User Not Found'), [], null);
            }

            $user->is_deleted = 1;
            $user->save();
            return $this->responseWithSuccess(__('account_deleted'), [], 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something Went Wrong'), [], null);
        }
    }
}
