<?php

namespace App\Http\Controllers\Site;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SiteResource\BrandPaginateResource;
use App\Http\Resources\SiteResource\CampaignPaginateResource;
use App\Http\Resources\SiteResource\CategoryResource;
use App\Http\Resources\SiteResource\ProductPaginateResource;
use App\Http\Resources\SiteResource\ShopPaginateResource;
use App\Http\Resources\SiteResource\VideoPaginateResource;
use App\Http\Resources\SiteResource\WishlistResource;
use App\Repositories\Admin\Page\PageRepository;
use App\Repositories\Interfaces\Admin\Addon\VideoShoppingInterface;
use App\Repositories\Interfaces\Admin\Blog\BlogInterface;
use App\Repositories\Interfaces\Admin\CurrencyInterface;
use App\Repositories\Interfaces\Admin\LanguageInterface;
use App\Repositories\Interfaces\Admin\Marketing\CampaignInterface;
use App\Repositories\Interfaces\Admin\Marketing\SubscriberInterface;
use App\Repositories\Interfaces\Admin\MediaInterface;
use App\Repositories\Interfaces\Admin\OrderInterface;
use App\Repositories\Interfaces\Admin\Product\BrandInterface;
use App\Repositories\Interfaces\Admin\Product\CategoryInterface;
use App\Repositories\Interfaces\Admin\Product\ProductInterface;
use App\Repositories\Interfaces\Admin\SellerInterface;
use App\Repositories\Interfaces\Site\AddressInterface;
use App\Repositories\Interfaces\Site\CartInterface;
use App\Repositories\Interfaces\Site\ContactUsInterface;
use App\Repositories\Interfaces\Site\ReviewInterface;
use App\Repositories\Interfaces\Site\WishlistInterface;
use App\Traits\HomePage;
use Illuminate\Http\Request;

class FrontendController extends Controller
{
    use HomePage;

    protected $product;
    protected $review;
    protected $blog;

    public function __construct(ProductInterface $product, ReviewInterface $review, BlogInterface $blog)
    {
        $this->product = $product;
        $this->review = $review;
        $this->blog = $blog;
    }

    public function home(MediaInterface $media, CategoryInterface $category, SellerInterface $seller, BrandInterface $brand, CampaignInterface $campaign,VideoShoppingInterface $shopping): \Illuminate\Http\JsonResponse
    {
        try {
            $data           = $this->parseSettingsData($media, $category, $seller, $brand, $campaign,$shopping);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function contactPage(PageRepository $pageRepository): \Illuminate\Http\JsonResponse
    {

        try {
            $data = [
                'contact'       => $pageRepository->contactPage(),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function page(Request $request,PageRepository $pageRepository): \Illuminate\Http\JsonResponse
    {
        try {
            $page = $pageRepository->pageBySlug($request->slug);
            $data = [
                'page' => [
                    'title'     => $page->getTranslation('title',languageCheck()),
                    'link'      => $page->link,
                    'content'   => $page->getTranslation('content',languageCheck())
                ]
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function campaigns(CampaignInterface $campaign): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'campaigns' => new CampaignPaginateResource($campaign->campaigns(12))
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function categories(CategoryInterface $category): \Illuminate\Http\JsonResponse
    {

        try {
            $data = [
                'categories' => CategoryResource::collection($category->categoryPage())
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function dailyDeals(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'products' => new ProductPaginateResource($this->product->dailyDeals($request->paginate))
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function cartPage(CartInterface $cart): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'carts' => $cart->userCart(),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function checkout(AddressInterface $address): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'addresses' => $address->userAddress(),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function trackOrder(Request $request, OrderInterface $order): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'order' => $order->orderByCode($request->all())
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function brands(BrandInterface $brand): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'brands' => new BrandPaginateResource($brand->allBrands())
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function sellers(SellerInterface $seller,Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'sellers' => settingHelper('seller_system') == 1 ? new ShopPaginateResource($seller->allSeller($request->all())) : []
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Oops...Something Went Wrong'
            ]);
        }
    }

    public function contactUs(ContactUsInterface $contactUs, Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required',
            'subject' => 'required',
            'message' => 'required',
        ]);
        try {
            $data = [
                'contact' => $contactUs->storeContact($request),
                'success' => __('Message Sent Successfully'),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function reply(ContactUsInterface $contactUs, Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'reply' => $contactUs->reply($request),
                'success' => __('Reply Sent Successfully'),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function changeLocale(LanguageInterface $language, $locale): \Illuminate\Http\JsonResponse
    {
        try {
            session()->put('lang', $locale);
            $language = $language->getByLocale($locale);
            $data = [
                'active_language'   => $language,
            ];

            session()->put('text_direction',$language->text_direction);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' => __( 'Oops...Something Went Wrong')
            ]);
        }

    }

    public function langKeywords(): \Illuminate\Http\JsonResponse
    {
        try {

            $lang = languageCheck();

            $data = [
                'lang'=> file_exists(base_path('resources/lang/' . $lang . '.json')) ? json_decode(file_get_contents(base_path('resources/lang/' . $lang . '.json'))) :
                    json_decode(file_get_contents(base_path('resources/lang/en.json')))
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }

    }

    public function changeCurrency(CurrencyInterface $currency, $id): \Illuminate\Http\JsonResponse
    {
        try {
            session()->put('currency', $id);
            $data = [
                'active_currency' => $currency->get($id),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function wishlist(WishlistInterface $wislist): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'wishlist' => new WishlistResource($wislist->userWishlist(10)),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function subscribers(Request $request, SubscriberInterface $subscriber): \Illuminate\Http\JsonResponse
    {

        $request->validate([
            'email' => 'required|email|unique:subscribers',
        ]);
        try {
            $data = [
                'subscribe' => $subscriber->store($request->email),
                'success' => __('You have subscribed successfully'),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }

    public function videoShopping(VideoShoppingInterface $shopping): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'videos' => new VideoPaginateResource($shopping->all()->active()->SellerCheck()->paginate(12)),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }
    public function videoShoppingDetails(VideoShoppingInterface $shopping,$slug): \Illuminate\Http\JsonResponse
    {
        try {
            $data = [
                'video' => $shopping->shopBySlug($slug),
            ];
            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'error' =>  __( 'Oops...Something Went Wrong')
            ]);
        }
    }
}
