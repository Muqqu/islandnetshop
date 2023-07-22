<?php

namespace App\Http\Controllers\Api\V100;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\CategoryWithoutChildResource;
use App\Http\Resources\BlogResource;
use App\Http\Resources\BrandResource;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CategoryProductResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\SellerResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SiteResource\VideoResource;
use App\Http\Resources\SliderResource;
use App\Http\Resources\TopSellerResource;
use App\Repositories\Interfaces\Admin\Addon\VideoShoppingInterface;
use App\Repositories\Interfaces\Admin\Blog\BlogInterface;
use App\Repositories\Interfaces\Admin\Marketing\CampaignInterface;
use App\Repositories\Interfaces\Admin\MediaInterface;
use App\Repositories\Interfaces\Admin\Product\BrandInterface;
use App\Repositories\Interfaces\Admin\Product\CategoryInterface;
use App\Repositories\Interfaces\Admin\Product\ProductInterface;
use App\Repositories\Interfaces\Admin\SellerInterface;
use App\Repositories\Interfaces\Admin\Service\ServiceInterface;
use App\Repositories\Interfaces\Admin\Slider\SliderInterface;
use App\Traits\ApiReturnFormatTrait;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    use ApiReturnFormatTrait;

    public function parseMobileSettingsData($media, $category, $seller, $brand, $campaign, $product, $blog, $slider, $service, $shopping, $request)
    {

        $settings = settingHelper('mobile_home_page_contents');

        $results[] = [
            'section_type' => 'categories',
            'categories' => CategoryWithoutChildResource::collection($category->mobileCategory(get_pagination('api_paginate'))),
        ];
        $results[] = [
            'section_type' => 'slider',
            'slider' => SliderResource::collection($slider->homeScreenSliders()),
        ];

        if (settingHelper('mobile_service_info_section') == 1) {
            $results[] = [
                'section_type' => 'benefits',
                'benefits' => ServiceResource::collection($service->frontendService()),
            ];
        }

        if ($settings) {

            $data = array();
            foreach ($settings as $key => $setting) {
                foreach ($setting as $set_key => $item) {
                    if ($set_key == 'banner') {
                        $data['section_title'] = 'banner';
                        $banners = [];

                        $action_types = $item['action_type'];
                        foreach ($item['thumbnail'] as $key => $thumbnail) {
                            $image = $media->get($thumbnail);
                            $action_title = '';
                            $action_id = '';
                            if ($action_types[$key] == 'product') {
                                $action_id = $item['action_to'][$key][$key];
                                $banner_product = $product->get($action_id);
                                $action_title =$banner_product ? nullCheck($banner_product->getTranslation('name', apiLanguage($request->lang))) : '';
                            } else if ($action_types[$key] == 'category') {
                                $action_id = $item['action_to'][$key][$key];
                                $banner_category = $category->get($action_id);
                                $action_title = $banner_category ? nullCheck($banner_category->getTranslation('title', apiLanguage($request->lang))) : '';
                            } else if ($action_types[$key] == 'brand') {
                                $action_id = $item['action_to'][$key][$key];
                                $banner_brand = $brand->get($action_id);
                                $action_title = $banner_brand ? nullCheck($banner_brand->getTranslation('title', apiLanguage($request->lang))) : '';

                            } else if ($action_types[$key] == 'seller') {
                                $action_id = $item['action_to'][$key][$key];
                                $banner_seller = $seller->getSeller($action_id);
                                $action_title = $banner_seller ? nullCheck($banner_seller->shop_name) : '';
                            } else if ($action_types[$key] == 'blog') {
                                $action_id = $item['action_to'][$key][$key];
                                $banner_blog = $blog->get($action_id);
                                $action_title = $banner_blog ? nullCheck($banner_blog->getTranslation('title', apiLanguage($request->lang))) : '';
                            } else if ($action_types[$key] == 'url') {
                                $action_title = nullCheck($item['action_to'][$key][$key]);
                            }

                            $banners[] = [
                                'thumbnail' => @is_file_exists($image->image_variants['image_300x170'], $image->image_variants['storage']) ? @get_media($image->image_variants['image_300x170'], $image->image_variants['storage']) : static_asset('images/default/default-image-300x170.png'),
                                'action_type' => $action_types[$key],
                                'action_to' => $action_title,
                                'action_id' => $action_id,
                            ];
                        }
                        $results = $this->keyDefine('banners', $key, $banners, $results);
                    }
                    if ($set_key == 'campaign') {
                        $data['section_title'] = 'campaign';
                        $campaigns = CampaignResource::collection($campaign->campaignByIds($item));
                        $results = $this->keyDefine('campaigns', $key, $campaigns, $results);
                    }
                    if ($set_key == 'popular_category') {
                        $data['section_title'] = 'popular_categories';
                        $popular_category = CategoryWithoutChildResource::collection($category->categoryByIds($item));
                        $results = $this->keyDefine('popular_categories', $key, $popular_category, $results);
                    }
                    if ($set_key == 'top_category') {
                        $top_categories = CategoryWithoutChildResource::collection($category->categoryByIds($item, 8));
                        $results = $this->keyDefine('top_categories', $key, $top_categories, $results);
                    }
                    if ($set_key == 'todays_deal') {
                        $todays_deal = ProductResource::collection($product->todayDeals());
                        $results = $this->keyDefine('today_deals', $key, $todays_deal, $results);
                    }
                    if ($set_key == 'flash_deal') {
                        $flash_products = ProductResource::collection($product->campaignProducts());
                        $results = $this->keyDefine('flash_deals', $key, $flash_products, $results);
                    }
                    if ($set_key == 'latest_product') {
                        $latest_products = ProductResource::collection($product->latestProducts());
                        $results = $this->keyDefine('latest_products', $key, $latest_products, $results);
                    }
                    if ($set_key == 'category_section') {
                        $category_products = new CategoryProductResource($category->categoryProducts($item['category']));
                        $results = $this->keyDefine('category_section', $key, $category_products, $results);
                    }
                    if ($set_key == 'best_selling_products') {
                        $best_selling_products = ProductResource::collection($product->bestSelling());
                        $results = $this->keyDefine('best_selling_products', $key, $best_selling_products, $results);
                    }
                    if ($set_key == 'offer_ending_soon') {
                        $offer_end = ProductResource::collection($product->offerEndingSoon());
                        $results = $this->keyDefine('offer_ending', $key, $offer_end, $results);
                    }
                    if ($set_key == 'latest_news') {
                        $blogs = BlogResource::collection($blog->homePageBlogs());
                        $results = $this->keyDefine('latest_news', $key, $blogs, $results);

                    }
                    if ($set_key == 'popular_brands') {
                        $brands = BrandResource::collection($brand->homePageBrands());
                        $results = $this->keyDefine('popular_brands', $key, $brands, $results);
                    }
                    if ($set_key == 'top_sellers') {
                        $sellers = settingHelper('seller_system') == 1 ? TopSellerResource::collection($seller->homePageSellers()) : [];
                        $results = $this->keyDefine('top_shops', $key, $sellers, $results);
                    }
                    if ($set_key == 'best_sellers') {
                        $best_sellers = settingHelper('seller_system') == 1 ? SellerResource::collection($seller->homePageBestSellers()) : [];
                        $results = $this->keyDefine('best_shops', $key, $best_sellers, $results);
                    }
                    if ($set_key == 'featured_sellers') {
                        $featured_sellers = settingHelper('seller_system') == 1 ? SellerResource::collection($seller->homePageFeaturedSellers($item)) : [];
                        $results = $this->keyDefine('featured_shops', $key, $featured_sellers, $results);
                    }
                    if ($set_key == 'express_sellers') {
                        $express_sellers = settingHelper('seller_system') == 1 ? TopSellerResource::collection($seller->homePageExpressSellers($item)) : [];
                        $results = $this->keyDefine('express_shops', $key, $express_sellers, $results);
                    }
                    if ($set_key == 'video_shopping' && addon_is_activated('video_shopping')) {
                        $videos = VideoResource::collection($shopping->all()->active()->SellerCheck()->take(4)->get());

                        $results = $this->keyDefine('video_shopping', $key, $videos, $results);

                    }
                }
            }
        }


        if (settingHelper('mobile_recent_viewed_products') == 1) {
            $results[] = [
                'section_type' => 'recent_viewed_product',
                'recent_viewed_product' => ProductResource::collection($product->viewedProduct()),
            ];
        }

        if (settingHelper('mobile_subscription_section') == 1) {
            $results[] = [
                'section_type' => 'subscription_section',
                'subscription_section' => true,
            ];
        }

        return $results;
    }

    protected function keyDefine($key, $index, $data, $results)
    {
        $results[] = [
            'section_type' => $key,
            $key => $data,
        ];
        return $results;
    }

    public function homePageData(MediaInterface  $media, CategoryInterface $category, SellerInterface $seller, BrandInterface $brand, CampaignInterface $campaign, ProductInterface $product, BlogInterface $blog,
                                 SliderInterface $slider, ServiceInterface $service, VideoShoppingInterface $shopping, Request $request)
    {
        try {
            $data = $this->parseMobileSettingsData($media, $category, $seller, $brand, $campaign, $product, $blog, $slider, $service, $shopping, $request);

            return $this->responseWithSuccess(__('Date Fetched Successfully'), $data, 200);
        } catch (\Exception $e) {
            return $this->responseWithError(__('Something Went Wrong'), [], null);
        }
    }
}