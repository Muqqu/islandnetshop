<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Traits\ImageTrait;
use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use DB;
use Sentinel;
use Activation;
class SocialController extends Controller
{
    use ImageTrait;
    public function redirect($provider)
    {
//        try {
            $redirect = Socialite::driver($provider)->redirect();
            return $redirect->getTargetUrl();
//        } catch (\Exception $e){
//            dd($e->getMessage());
//            return redirect('login')->with(['error' => __('Something went wrong. Please try again.')]);
//        }
    }

    public function callback($provider)
    {
        try{
            $userSocial 	= Socialite::driver($provider)->user();
            $avater = $provider == 'twitter' ? $userSocial->avatar_original : $userSocial->getAvatar();

            if (!blank($avater)) {
                $image_response = $this->getImageWithRecommendedSize(null,[],[],false,$avater);
            }

            $user       	= User::where('email', $userSocial->getEmail())->first();
            $name           = explode(' ', $userSocial->getName());
            $credentials = [
                'first_name'     => $name[0],
                'last_name'      => $name[1].(@$name[2] ? ' '.$name[2] : ''),
                'email'          => $userSocial->getEmail(),
                'images'         => $image_response['images'] ?? [],
                'password'       => 'social-login',
                'is_password_set'=> 0,
            ];

            if($user){

                if($user->is_user_banned == 1) {
                    return redirect()->back()->with(['error' => __('Your account is banned')]);
                } elseif($user->status == 0){
                    return redirect()->back()->with(['error' => __('Your account is inactive')]);
                }

                Sentinel::authenticate($user);
                return redirect()->route('home')->with(['success' => __('Login successfully')]);
            }else{
                $user               = Sentinel::register($credentials);
                $activation         = Activation::create($user);
                Activation::complete($user, $activation->code);

                Sentinel::authenticate($credentials);
                return redirect()->route('home')->with(['success' => __('Login successfully')]);
            }

        } catch(\Exception $e) {
            DB::rollBack();
            return redirect('login')->with(['error' => __('Something went wrong. Please try again.')]);

        }
    }
}
