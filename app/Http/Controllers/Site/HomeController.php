<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Resources\SiteResource\BannerResource;
use App\Http\Resources\SiteResource\PageResource;
use App\Http\Resources\SiteResource\ServiceResource;
use App\Http\Resources\SiteResource\SidebarCategoryResource;
use App\Http\Resources\SiteResource\SliderResource;
use App\Http\Resources\SiteResource\ViewedProductResource;
use App\Models\Media;
use App\Models\OrderDetail;
use App\Repositories\Interfaces\Admin\AddonInterface;
use App\Repositories\Interfaces\Admin\Blog\BlogInterface;
use App\Repositories\Interfaces\Admin\CurrencyInterface;
use App\Repositories\Interfaces\Admin\LanguageInterface;
use App\Repositories\Interfaces\Admin\Page\PageInterface;
use App\Repositories\Interfaces\Admin\Product\BrandInterface;
use App\Repositories\Interfaces\Admin\Product\CategoryInterface;
use App\Repositories\Interfaces\Admin\Product\ProductInterface;
use App\Repositories\Interfaces\Admin\SellerProfileInterface;
use App\Repositories\Interfaces\Admin\Service\ServiceInterface;
use App\Repositories\Interfaces\Admin\Slider\BannerInterface;
use App\Repositories\Interfaces\Admin\Slider\SliderInterface;
use App\Repositories\Interfaces\Site\CartInterface;
use App\Repositories\Interfaces\Site\WishlistInterface;
use App\Traits\HomePage;
use App\Traits\MetaGeneratorTrait;
use App\Traits\ResetPasswordTrait;
use App\Utility\AppSettingUtility;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

class HomeController extends Controller
{
    use HomePage, MetaGeneratorTrait, ResetPasswordTrait;

    public function index(LanguageInterface $language,CurrencyInterface $currency, WishlistInterface $wishlist, CartInterface $cart, CategoryInterface $category, SliderInterface $slider,BannerInterface $banner,
                          ServiceInterface $service, ProductInterface $product,SellerProfileInterface $seller,BlogInterface $blog,BrandInterface $brand,AddonInterface $addon,$email=null,$resetCode=null)
    {
        if (config('app.mobile_mode') == 'on' || !is_dir('resources/views/admin/store-front'))
        {
            if (authUser())
            {
                return redirect()->route('dashboard');
            }
            return redirect()->route('admin.login.form');
        }

        try {
            if(request()->route()->getName() == 'seller.register'):
                if (settingHelper('seller_system') != 1):
                    return redirect('/');
                endif;
            endif;
            if(request()->route()->parameter('email'))
            {
                $this->resetPassword($email,$resetCode);
            }

            $meta = $this->generateMeta($product,$blog,$category,$brand,$seller);

            if (array_key_exists('url_exception',$meta) && $meta['url_exception'] == 1)
            {
                return redirect('page-not-found');
            }

            $components = [];
            $home_page_contents = settingHelper('home_page_contents') ? settingHelper('home_page_contents') : [];
            foreach ($home_page_contents as $key => $item) {
                foreach ($item as $k=> $component) {
                    $components[] = $k;
                }
            }

            $lang = languageCheck();


            if (session()->has('currency')) {
                $user_currency              = session()->get('currency');
            } else {
                $user_currency              = settingHelper('default_currency');
            }
            if (settingHelper('default_currency'))
            {
                $default_currency           = settingHelper('default_currency');
            }
            else{
                $default_currency = 1;
            }

            $data = [
                'settings'                          => $this->settingsData(),
                'languages'                         => $language->activeLanguages(),
                'currencies'                        => $currency->activeCurrency(),
                'user'                              => authUser() ? authUser()->makeHidden(['is_user_banned','permissions', 'newsletter_enable', 'otp', 'firebase_auth_id', 'created_at', 'updated_at', 'images', 'image_id']) : [],
                'active_language'                   => $language->getByLocale($lang),
                'active_currency'                   => $currency->get($user_currency),
                'default_currency'                  => $currency->get($default_currency),
                'wishlists'                         => $wishlist->getHeaderWishlist(),
                'shop_follower'                     => authUser() && settingHelper('seller_system') == 1 ? $seller->shopFollower() : '',
                'carts'                             => $this->cartList($cart->all()),
                'categories'                        => [],
                'sliders'                           => SliderResource::collection($slider->frontendSliders()),
                'banners'                           => BannerResource::collection($banner->frontendBanners()),
                'services'                          => settingHelper('show_service_info_section') == 1 ? ServiceResource::collection($service->frontendService()) : [],
                'viewed_products'                   => [],
                'pages'                             => [],
                'compare_list'                      => $product->compareList(),
                'home_components'                   => $components,
                'meta'                              => $meta,
                'addons'                            => $addon->activePlugin(),
                'favicon'                           => [
                    'image_16x16'                   => @is_file_exists(@settingHelper('favicon')['image_16x16_url']) ? get_media(settingHelper('favicon')['image_16x16_url']) : static_asset('images/ico/favicon.ico'),
                    'image_144x144'                 => @is_file_exists(@settingHelper('favicon')['image_144x144_url']) ? get_media(settingHelper('favicon')['image_144x144_url']) : static_asset('images/ico/apple-touch-icon-precomposed.png'),
                    'image_114x114'                 => @is_file_exists(@settingHelper('favicon')['image_144x144_url']) ? get_media(settingHelper('favicon')['image_114x114_url']) : static_asset('images/ico/apple-touch-icon-114-precomposed.png'),
                    'image_72x72'                   => @is_file_exists(@settingHelper('favicon')['image_72x72_url']) ? get_media(settingHelper('favicon')['image_72x72_url']) : static_asset('images/ico/apple-touch-icon-72-precomposed.png'),
                    'image_57x57'                   => @is_file_exists(@settingHelper('favicon')['image_57x57_url']) ? get_media(settingHelper('favicon')['image_57x57_url']) : static_asset('images/ico/apple-touch-icon-57-precomposed.png'),
                ],
                'default_assets' => $this->defaultAssets(),
            ];
            return view('frontend.master', $data);
        } catch (\Exception $e) {
            return $e;
        }
    }

    public function settingsData(): array
    {

        $lang = languageCheck();

        $popup_modal = [];

        $stripe                         = settingData(get_yrsetting('is_stripe_activated'));
        $social_links                   = settingData(['facebook_link', 'twitter_link', 'instagram_link', 'youtube_link', 'linkedin_link']);
        $footer_data                    = settingData(['footer_contact_phone', 'footer_contact_email', 'footer_contact_address', 'copyright', 'about_description']);
        $currency_setting               = settingData(['no_of_decimals', 'decimal_separator', 'currency_symbol_format']);
        $header_data                    = settingData(['default_language', 'system_name', 'default_currency', 'header_contact_phone', 'header_contact_email', 'language_switcher', 'currency_switcher','seller_system','topbar_play_store_link','topbar_app_store_link','header_contact_number']);
        $store_links                    = settingData(['play_store_link', 'apple_store_link']);
        $other_data                     = settingData(['is_google_login_activated', 'is_facebook_login_activated', 'is_twitter_login_activated']);
        $recaptcha                      = settingData(['is_recaptcha_activated', 'recaptcha_Site_key']);
        $modules                        = settingData(['seller_system', 'color','pickup_point','wallet_system','coupon_system','pay_later_system']);
        $agreements                     = settingData(['seller_agreement', 'customer_agreement','privacy_agreement','refund_policy_agreement']);
        $map                            = settingData(['map_api_key', 'zoom_level','latitude','longitude']);

        $menu = [
            'footer_menu'                       => settingHelper('footer_menu'),
            'header_menu'                       => settingHelper('header_menu'),
            'useful_links'                      => settingHelper('useful_links')
        ];

        $popup_array = ['popup_title', 'popup_description', 'popup_image','site_popup_status','popup_show_in'];
        foreach ($popup_array as $key => $pop_data):
            $popup_modal[$pop_data] = settingHelper($pop_data, $lang);
        endforeach;

        if (array_key_exists('popup_image',$popup_modal))
        {
            $popup_modal['popup_image'] = getFileLink('270x260',settingHelper('popup_image'));
        }

        $ngn_exchange_rate      = 1;
        $is_paystack_activated  = settingHelper('is_paystack_activated') == 1;
        $is_flutterwave_activated  = settingHelper('is_flutterwave_activated') == 1;
        $ngn = AppSettingUtility::currencies()->where('code','NGN')->first();
        if($ngn):
            $ngn_exchange_rate     = $ngn->exchange_rate;
        else:
            $is_paystack_activated    = 0;
            $is_flutterwave_activated = 0;
        endif;

        $is_mollie_activated       = settingHelper('is_mollie_activated') == 1;

        $euro = AppSettingUtility::currencies()->where('code','EUR')->first();
        if(!$euro):
            $is_mollie_activated    = 0;
        endif;

        $settings = [
            'light_logo'                        => settingHelper('light_logo') != [] && @is_file_exists(settingHelper('light_logo')['original_image']) ?  get_media(@settingHelper('light_logo')['original_image'], @settingHelper('light_logo')['storage']) : static_asset('images/default/light-logo-100x38.png'),
            'dark_logo'                         => settingHelper('dark_logo') != [] && @is_file_exists(settingHelper('dark_logo')['original_image']) ?  get_media(@settingHelper('dark_logo')['original_image'], @settingHelper('dark_logo')['storage']) : static_asset('images/default/dark-logo.png'),
            'subscription_section'              => settingHelper('show_subscription_section'),
            'article_section'                   => settingHelper('show_blog_section'),
            'recent_viewed'                     => settingHelper('show_recent_viewed_products'),
            'category_Section'                  => settingHelper('show_categories_section'),
            'article'                           => settingHelper('home_page_article'),
            'show_social_links'                 => settingHelper('social_link_status'),
            'show_service_info_section'         => settingHelper('show_service_info_section'),
            'payment_method_banner'             => @get_media(@settingHelper('payment_method_banner')['image_48x25'], @settingHelper('payment_method_banner')['storage']),
            'login_banner'                      => @getFileLink('320x520',settingHelper('login_banner')['images']),
            'sign_up_banner'                    => @getFileLink('320x520',settingHelper('sing_up_banner')['images']),
            'seller_sing_up_banner'             => @getFileLink('320x852',settingHelper('seller_sing_up_banner')['images']),
            'forgot_password_banner'            => @getFileLink('320x520',settingHelper('forgot_password_banner')['images']),
            'user_dashboard_banner'             => @getFileLink('940x110',settingHelper('user_dashboard_banner')['images']),
            'product_details_site_banner'       => @get_media(@settingHelper('product_details_site_banner')['images']['image_263x263'], @settingHelper('product_details_site_banner')['images']['storage']),
            'category_default_banner'           => @getFileLink('835x200',settingHelper('category_default_banner')['images']),
            'visa_pay_banner'                   => settingHelper('visa_pay_banner') == 1,
            'master_card_pay_banner'            => settingHelper('master_card_pay_banner') == 1,
            'american_express_pay_banner'       => settingHelper('american_express_pay_banner') == 1,
            'paypal_payment_banner'             => settingHelper('paypal_payment_banner') == 1,
            'apple_pay_banner'                  => settingHelper('apple_pay_banner') == 1,
            'after_pay_banner'                  => settingHelper('after_pay_banner') == 1,
            'amazon_pay_banner'                 => settingHelper('amazon_pay_banner') == 1,
            'is_recaptcha_activated'            => settingHelper('is_recaptcha_activated'),
            'shipping_fee_type'                 => settingHelper('shipping_fee_type'),
            'header_theme'                      => settingHelper('header_theme'),
            'full_width_menu_background'        => settingHelper('full_width_menu_background'),
            'is_paypal_activated'               => settingHelper('is_paypal_activated'),
            'is_stripe_activated'               => settingHelper('is_stripe_activated'),
            'is_razorpay_activated'             => settingHelper('is_razorpay_activated'),
            'is_sslcommerz_activated'           => settingHelper('is_sslcommerz_activated'),
            'is_paytm_activated'                => settingHelper('is_paytm_activated'),
            'is_jazz_cash_activated'            => settingHelper('is_jazz_cash_activated'),
            'is_paystack_activated'             => $is_paystack_activated,
            'is_flutterwave_activated'          => $is_flutterwave_activated,
            'ngn_exchange_rate'                 => $ngn_exchange_rate,
            'is_mollie_activated'               => $is_mollie_activated,
            'reward_convert_rate'               => settingHelper('reward_convert_rate'),
            'refund_with_shipping_cost'         => settingHelper('refund_with_shipping_cost'),
            'refund_request_time'               => settingHelper('refund_request_time'),
            'wholesale_price_variations_show'   => settingHelper('wholesale_price_variations_show'),
            'gdpr'                              => settingHelper('cookies_agreement', $lang),
            'gdpr_enable'                       => settingHelper('cookies_status'),
            'footer_logo'                       => settingHelper('footer_logo') != [] && @is_file_exists(settingHelper('footer_logo')['image_89x33']) ? get_media(settingHelper('footer_logo')['image_89x33'],settingHelper('footer_logo')['storage']) : static_asset('images/default/logo-89x33.png'),
            'text_direction'                    => session()->has('text_direction') ? session()->get('text_direction') : 'ltl',
            'demo_mode'                         => isDemoServer(),
            'ssl_sandbox'                       => settingHelper('is_sslcommerz_sandbox_mode_activated'),
            'razor_key'                         => settingHelper('razorpay_key'),
            'paypal_key'                        => settingHelper('paypal_client_id'),
            'current_version'                   => settingHelper('current_version'),
            'shipping_cost'                     => settingHelper('shipping_fee_type'),
            'system_name'                       => settingHelper('system_name'),
            'default_country'                   => settingHelper('default_country'),
            'menu_background_color'             => settingHelper('menu_background_color'),
            'pushar_activated'                  => settingHelper('is_pusher_notification_active') == 1,
            'flw_public_key'                    => settingHelper('flutterwave_public_key'),
            'paystack_pk'                       => settingHelper('paystack_public_key'),
            'refund_sticker'                    => settingHelper('refund_sticker') != [] && @is_file_exists(settingHelper('refund_sticker')['image_45x45'] , settingHelper('refund_sticker')['storage'])  ?  get_media(@settingHelper('refund_sticker')['image_45x45'] , settingHelper('refund_sticker')['storage']) : static_asset('images/others/policy-icon.svg'),
            'refund_protection_title'           => settingHelper('refund_protection_title', $lang),
            'refund_protection_sub_title'       => settingHelper('refund_protection_sub_title', $lang),
            'tax_type'                          => settingHelper('refund_protection_sub_title', $lang),
        ];
        return array_merge($settings,$other_data,$store_links,$header_data,$menu,$footer_data,$social_links,$stripe,$currency_setting,$popup_modal,$recaptcha,$modules,$agreements,$map);

    }

    public function defaultAssets(): array
    {
        return [
            'phone_image'                   => static_asset('images/others/phone.svg'),
            'compare_image'                  => static_asset('images/others/compare.svg'),
            'wishlist_svg'                  => static_asset('images/others/wishlist.svg'),
            'remove_wishlist'               => static_asset('images/others/remove-wishlist.svg'),
            'heart_image'                   => static_asset('images/others/heart.svg'),
            'bag_image'                     => static_asset('images/others/bag.svg'),
            'search_image'                  => static_asset('images/others/search.svg'),
            'pencil_image'                  => static_asset('images/others/daily-deal.svg'),
            'coupon_image'                  => static_asset('images/others/pencil1.png'),
            'gift_image'                    => static_asset('images/others/gift.png'),
            'all_offers'                    => static_asset('images/others/all-offer.svg'),
            'category_image'                => static_asset('images/others/3.svg'),
            'review_image'                  => static_asset('images/others/env.svg'),
            'list'                          => static_asset('images/others/list.svg'),
            'home'                          => static_asset('images/others/home.svg'),
            'shopping_bag'                  => static_asset('images/others/shopping-bag.svg'),
            'user'                          => static_asset('images/others/user.svg'),
            'razor_pay'                     => static_asset('images/payment-method/razorpay.png'),
            'stripe'                        => static_asset('images/payment-method/stripe.png'),
            'paypal'                        => static_asset('images/payment-method/paypal.png'),
            'paytm'                         => static_asset('images/payment-method/paytm.png'),
            'ssl_commerze'                  => static_asset('images/payment-method/ssl_commerze.png'),
            'jazzCash'                      => static_asset('images/payment-method/JazzCash.png'),
            'mollie'                        => static_asset('images/payment-method/mollie.png'),
            'pay_stack'                     => static_asset('images/payment-method/paystack.png'),
            'fw'                            => static_asset('images/payment-method/fw.png'),
            'paylater'                      => static_asset('images/payment-method/paylater.jpg'),
            'cash'                          => static_asset('images/payment-method/cash.png'),
            'preloader'                     => static_asset('images/default/preloader.gif'),
            'shimmer'                       => static_asset('images/default/preview.jpg'),
            'google_play'                   => static_asset('images/others/play-store-logo.png'),
            'apple_store'                   => static_asset('images/others/app-store-logo.png'),
            'static_asset'                  => static_asset(),
            'captcha'                       => static_asset('frontend/js/api.js'),
            'razor_pay_js'                  => static_asset('frontend/js/razor_pay_checkout.js'),
            'compare_svg'                   => static_asset('images/others/compare.svg'),
            'menu_svg'                      => static_asset('images/others/menu.svg'),

            //frontend payment banner
            'visa'                          => static_asset('images/payment-method/visa.svg'),
            'master_card'                   => static_asset('images/payment-method/master-card.svg'),
            'american_express'              => static_asset('images/payment-method/american-express.svg'),
            'paypal_svg'                    => static_asset('images/payment-method/paypal.svg'),
            'apple_pay'                     => static_asset('images/payment-method/apple-pay.svg'),
            'amazon_pay'                    => static_asset('images/payment-method/amazon-pay.svg'),
            'after_pay'                     => static_asset('images/payment-method/after-pay.svg'),
        ];
    }

    public function sidebarCategories(CategoryInterface $category): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'categories'  => SidebarCategoryResource::collection($category->homePageCategory()),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( __('Oops...Something Went Wrong'))
            ]);
        }
    }

    public function viewedProducts(ProductInterface $product): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'viewed_products' =>  ViewedProductResource::collection($product->viewedProduct())
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __('Oops...Something Went Wrong')
            ]);
        }
    }

    public function activate()
    {
        return view('auth.mail.activate-account-email');
    }

    public function forget()
    {
        return view('auth.mail.forgot-password-email');
    }

    public function success()
    {
        return view('auth.mail.registration-success-email');
    }

    public function reset()
    {
        return view('auth.mail.reset-success-email');
    }

    public function fileDownload(Request $request, $id)
    {
        try {
            $orderDetails = OrderDetail::find($request->od);
            if ($request->u == authId() && $orderDetails->order->user_id == authId()):
                $media = Media::find($id);
                $file_path = public_path($media->original_file);
                return response()->download($file_path);
            else:
                abort(404);
//                return redirect()->route('home');
            endif;
        } catch (\Exception $e) {

        }
    }

    public function textDirection($dir): \Illuminate\Http\RedirectResponse
    {
        session()->put('text_direction',$dir);

        if ($dir == 'ltr')
        {
            session()->put('lang', 'en');
        }
        else{
            session()->put('lang', 'ar');
        }
        return back();
    }

    public function summernoteClean(Request $request)
    {
        return clean($request->value);
    }

    public function passwordVerify(Request $request): \Illuminate\Http\RedirectResponse
    {
        $request->validate([
            'password' => 'required'
        ]);

        if (Hash::check($request->password,authUser()->password))
        {
            return redirect()->back()->with(['success' => $request->api_id]);
        }
        else{
            Toastr::error(__('password_doesnt_match'),'Error!');
            return back();
        }
    }

    public function cacheClear()
    {
        try {
            Artisan::call('optimize:clear');
            cache()->flush();
            Toastr::success(__('cache_cleared_successfully'));
            return back();
        } catch (\Exception $e) {
            Toastr::error(__('Oops...Something Went Wrong'),'Error!');
            return back();
        }
    }
}
