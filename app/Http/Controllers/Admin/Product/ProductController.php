<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\Product\ProductStoreRequest;
use App\Models\ProductStock;
use App\Repositories\Interfaces\Site\ReviewInterface;
use App\Utility\VariantUtility;
use App\Repositories\Admin\VatTaxRepository;
use App\Repositories\Interfaces\Admin\LanguageInterface;
use App\Repositories\Interfaces\Admin\Product\AttributeInterface;
use App\Repositories\Interfaces\Admin\Product\BrandInterface;
use App\Repositories\Interfaces\Admin\Product\CategoryInterface;
use App\Repositories\Interfaces\Admin\Product\ColorInterface;
use App\Repositories\Interfaces\Admin\Product\ProductInterface;
use App\Repositories\Interfaces\Admin\SellerInterface;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected $products;
    protected $categories;
    protected $brands;
    protected $colors;
    protected $attributes;
    protected $vat_tax;
    protected $languages;
    protected $sellers;

    public function __construct(ProductInterface $products,
                                CategoryInterface $categories,
                                BrandInterface $brands,
                                ColorInterface $colors,
                                AttributeInterface $attributes,
                                VatTaxRepository $vat_tax,
                                SellerInterface $sellers,
                                LanguageInterface $languages){
        $this->products         = $products;
        $this->categories       = $categories;
        $this->brands           = $brands;
        $this->colors           = $colors;
        $this->attributes       = $attributes;
        $this->vat_tax          = $vat_tax;
        $this->languages        = $languages;
        $this->sellers          = $sellers;
    }
    public function index(Request $request, $status = null){
        try {
            $products           = $this->products->paginate($request, $status ,get_pagination('pagination'),'');

            $selected_category  = isset($request->c) ? $this->categories->get($request->c) : null;
            $selected_seller    = isset($request->sl) ? $this->sellers->getSeller($request->sl) : null;

            return view('admin.products.products.index', compact('status','products','selected_category','selected_seller'));
        } catch (\Exception $e) {
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }
    public function adminProducts(Request $request, $status = null){
        try {
            $products       = $this->products->paginate($request, $status ,get_pagination('pagination'),'admin');
            $sellers        = $this->sellers->all()->where('is_user_banned',0)->where('status',1)->get();

            $selected_category  = isset($request->c) ? $this->categories->get($request->c) : null;

            return view('admin.products.products.admin-products', compact('status','products','selected_category','sellers'));
        } catch (\Exception $e) {
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }
    public function sellerProducts(Request $request, $status = null){
        try {
            $products           = $this->products->paginate($request, $status ,get_pagination('pagination'),'seller');

            $selected_category  = isset($request->c) ? $this->categories->get($request->c) : null;
            $selected_seller    = isset($request->sl) ? $this->sellers->getSeller($request->sl) : null;

            return view('admin.products.products.seller-products', compact('status','products','selected_category','selected_seller'));
        } catch (\Exception $e) {
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }
    public function digitalProducts(Request $request, $status = null){
        try {
            $products           = $this->products->paginate($request, $status ,get_pagination('pagination'),'digital');

            $selected_category  = isset($request->c) ? $this->categories->get($request->c) : null;
            $selected_seller    = isset($request->sl) ? $this->sellers->getSeller($request->sl) : null;

            return view('admin.products.products.digital-products', compact('status','products','selected_category','selected_seller'));
        } catch (\Exception $e) {
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }
    public function catalogProducts(Request $request, $status = null){
        try {
            $products       = $this->products->paginate($request, $status ,\Config::get('yrsetting.paginate'),'catalog');

            $selected_category  = isset($request->c) ? $this->categories->get($request->c) : null;
            $selected_seller    = isset($request->sl) ? $this->sellers->getSeller($request->sl) : null;

            return view('admin.products.products.catalog-products', compact('status','products','selected_category','selected_seller'));
        } catch (\Exception $e) {
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }

    public function classifiedProducts(Request $request, $status = null){
        try {
            $products           = $this->products->paginate($request, $status ,\Config::get('yrsetting.paginate'),'classified');
            $selected_category  = isset($request->c) ? $this->categories->get($request->c) : null;
            $selected_seller    = isset($request->sl) ? $this->sellers->getSeller($request->sl) : null;
            return view('admin.products.products.classified-products', compact('status','products','selected_category','selected_seller'));
        } catch (\Exception $e) {
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }

    public function create(Request $request){
        $categories     = $this->categories->allCategory()->where('parent_id', null)->where('status',1);
        $brands         = $this->brands->all()->where('lang','en')->where('status',1)->get();
        $colors         = $this->colors->all()->where('lang', 'en')->get();
        $attributes     = $this->attributes->all()->where('lang', 'en')->get();
        $r              = $request->r != ''? $request->r : $request->server('HTTP_REFERER');
        return view('admin.products.products.form',compact('categories','brands','colors','attributes','r'));
    }
    public function createDigitalProduct(Request $request){
        $categories     = $this->categories->allCategory()->where('parent_id', null)->where('status',1);
        $brands         = $this->brands->all()->where('lang','en')->where('status',1)->get();
        $colors         = $this->colors->all()->where('lang', 'en')->get();
        $attributes     = $this->attributes->all()->where('lang', 'en')->get();
        $is_digital     = 1;
        $r              = $request->r != ''? $request->r : $request->server('HTTP_REFERER');
        return view('admin.products.products.form',compact('categories','brands','colors','attributes','is_digital','r'));
    }
    public function createCatalogProduct(Request $request){
        $categories     = $this->categories->allCategory()->where('parent_id', null)->where('status',1);
        $brands         = $this->brands->all()->where('lang','en')->where('status',1)->get();
        $colors         = $this->colors->all()->where('lang', 'en')->get();
        $attributes     = $this->attributes->all()->where('lang', 'en')->get();
        $is_catalog     = 1;
        $r              = $request->r != ''? $request->r : $request->server('HTTP_REFERER');
        return view('admin.products.products.form',compact('categories','brands','colors','attributes','is_catalog','r'));
    }
    public function createClassifiedProduct(Request $request){
        $categories     = $this->categories->allCategory()->where('parent_id', null)->where('status',1);
        $brands         = $this->brands->all()->where('lang','en')->where('status',1)->get();
        $colors         = $this->colors->all()->where('lang', 'en')->get();
        $attributes     = $this->attributes->all()->where('lang', 'en')->get();
        $is_classified  = 1;
        $r              = $request->r != ''? $request->r : $request->server('HTTP_REFERER');
        return view('admin.products.products.form',compact('categories','brands','colors','attributes','is_classified','r'));
    }

    public function edit($id, Request $request){
        try {
           // session()->forget('attributes');
            $languages  = $this->languages->all()->orderBy('id', 'asc')->get();

            $lang       = $request->lang != '' ? $request->lang : \App::getLocale();
            if ($this->products->get($id) && $product_language = $this->products->getByLang($id, $lang)):
                $categories     = $this->categories->allCategory()->where('parent_id', null)->where('status',1);
                $brands         = $this->brands->all()->where('lang','en')->where('status',1)->get();
                $colors         = $this->colors->all()->where('lang', 'en')->get();
                $attributes     = $this->attributes->all()->where('lang', 'en')->get();
                $r              = $request->r != ''? $request->r : $request->server('HTTP_REFERER');
                return view('admin.products.products.edit',compact('languages', 'lang','product_language','categories','brands','attributes','colors','r'));

            else:
                Toastr::error(__('Not found'));
                return back();
            endif;
        } catch (\Exception $e){
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }

    public function statusChange(Request $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;

        if ($this->products->statusChange($request['data'])):
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

    public function store(ProductStoreRequest $request)
    {
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;

        if ($this->products->store($request)):
            Toastr::success(__('Created Successfully'));
            session()->forget('attributes');
            return redirect()->route('products');
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back()->withInput();
        endif;
    }

    public function update(Request $request)
    {
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;

        DB::beginTransaction();
        try{

            session()->forget('attributes');
            if ($request->variant_ids && count($request->variant_ids))
            {
                ProductStock::where('product_id',$request->id)->delete();
                session()->put('attributes',count($request->variant_ids));
            }

            $product = $this->products->get($request->id);

            $validator = Validator::make($request->all(), [
                'name'                      => 'required|max:190',
                'slug'                      => 'nullable|nullable|max:190|unique:products,slug,'.$request->id,
                'category'                  => 'required',
                'price'                     => 'numeric|required',
                'unit'                      => 'required',
                'variant_sku.*'             => 'required_if:has_variant,1|distinct|unique:product_stocks,sku',
                'video_url'                 => 'required_with:video_provider',
                'minimum_order_quantity'    => 'numeric|min:1',
                'low_stock_to_notify'       => 'numeric|min:0',
                'shipping_fee'              => 'required_if:shipping_type,flat_rate',
                'special_discount_period'   => 'required_with:special_discount_type',
                'special_discount'          => 'required_with:special_discount_type',

                'campaign_discount'         => 'required_with:campaign',
                'campaign_discount_type'    => 'required_with:campaign'
            ]);

            DB::commit();

            if ($validator->fails()) {
                DB::rollBack();
                return back()->withInput()->withErrors($validator);
            }

            if(!$request->has_variant)
            {
                $sku_validator = Validator::make($request->all(), [
                    'sku'      => 'required_without:has_variant|unique:product_stocks,sku,'.$product->stock()->first()->id,
                ]);

                if ($sku_validator->fails()) {
                    DB::rollBack();
                    return back()->withInput()->withErrors($sku_validator);
                }
            }
           
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->withInput()->withErrors($validator);
        }

        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;

        if ($this->products->update($request)):
            Toastr::success(__('Updated Successfully'));
            session()->forget('attributes');
            return redirect($request->r);
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back()->withInput();
        endif;
    }

    public function variants(Request $request)
    {
        if ($request->has_variant == 1):
            $variants = array();

            $product_price = $request->price;
            $colors       = false;

            if (!empty($request->colors)):
                array_push($variants, $request->colors);
                $colors = true;
            endif;

            if ($request->has('attribute_sets')):
                foreach ($request->attribute_sets as $key => $attribute_set):
                    $attribute_values = 'attribute_values_'.$attribute_set;
                    $values = array();
                    if ($request->has($attribute_values)):
                        foreach ($request[$attribute_values] as $value):
                            array_push($values, $value);
                        endforeach;
                    endif;
                    if($request->has($attribute_values)):
                        array_push($variants, $values);
                    endif;
                endforeach;
            endif;
            $variants_data = VariantUtility::getVariants($variants);
            if (!empty($variants_data[0])):
                return view('admin.products.products.variants', compact('variants','variants_data','product_price','colors'));
            else:
                return view('admin.products.products.variants');
            endif;
        else:
            return '';
        endif;
    }


    public function variantsEdit(Request $request)
    {
        $product = $this->products->get($request->id);
//        dd($product);
        if ($request->has_variant == 1):
            $variants = array();

            $product_price = $request->price;
            $colors       = false;

            if (!empty($request->colors)):
                array_push($variants, $request->colors);
                $colors = true;
            endif;

            if ($request->has('attribute_sets')):
                foreach ($request->attribute_sets as $key => $attribute_set):
                    $attribute_values = 'attribute_values_'.$attribute_set;
                    $values = array();
                    if ($request->has($attribute_values)):
                        foreach ($request[$attribute_values] as $value):
                            array_push($values, $value);
                        endforeach;
                    endif;
                    if($request->has($attribute_values)):
                        array_push($variants, $values);
                    endif;
                endforeach;
            endif;
            $variants_data = VariantUtility::getVariants($variants);
            if (!empty($variants_data[0])):
                return view('admin.products.products.variants_edit', compact('variants','variants_data','product_price','product','colors'));
            else:
                return view('admin.products.products.variants_edit');
            endif;
        else:
            return '';
        endif;
    }

    public function getAttributeValues(Request $request)
    {
        $attributes_sets = $request->attribute_sets;

        if (!empty($attributes_sets)):
            $attributes = $this->attributes->all()->whereIn('attributes.id', $attributes_sets)->where('lang','en')->get();
            return view('admin.products.products.values', compact('attributes','request','attributes_sets'));
        else:
            return '';
        endif;
    }

    public function restore($id){
        if ($this->products->restore($id)):
            Toastr::success(__('Updated successfully as unpublished'));
            return redirect()->back();
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }
    public function productStatusChange($status,$id){
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;

        if ($this->products->productStatusChange($status,$id)):
            Toastr::success(__('Updated successfully as '.$status));
            return redirect()->back();
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        endif;
    }

    public function cloneProduct(Request $request, $id){
        try {
            $languages  = $this->languages->all()->orderBy('id', 'asc')->get();

            $lang       = $request->lang != '' ? $request->lang : \App::getLocale();
            if ($this->products->get($id) && $product_language = $this->products->getByLang($id, $lang)):
                $categories     = $this->categories->allCategory()->where('parent_id', null)->where('status',1);
                $brands         = $this->brands->all()->where('lang','en')->where('status',1)->get();
                $colors         = $this->colors->all()->where('lang', 'en')->get();
                $attributes     = $this->attributes->all()->where('lang', 'en')->get();
                $r              = $request->r != ''? $request->r : $request->server('HTTP_REFERER');
                $clone          = 1;
                if($product_language->product->is_wholesale != 1):
                    return view('admin.products.products.edit',
                        compact(
                            'languages',
                            'lang',
                            'product_language',
                            'categories','brands',
                            'attributes',
                            'colors',
                            'r',
                            'clone'
                        ));
                else:
                    return redirect()->route('wholesale.product.clone', [$id]);
                endif;

            else:
                Toastr::error(__('Not found'));
                return back();
            endif;
        } catch (\Exception $e){
            Toastr::error(__('Something went wrong, please try again'));
            return back();
        }
    }

    public function StoreCloneProduct(ProductStoreRequest $request){
        if (isDemoServer()):
            Toastr::info(__('This function is disabled in demo server.'));
            return redirect()->back();
        endif;

        if ($this->products->store($request)):
            Toastr::success(__('Created Successfully'));
            return redirect($request->r);
        else:
            Toastr::error(__('Something went wrong, please try again'));
            return back()->withInput();
        endif;
    }

    public function reviews(ReviewInterface $review){
        $reviews = $review->paginateReviews(get_pagination('pagination'));
        return view('admin.products.products.review',compact('reviews'));
    }


    public function reviewStatusChange(ReviewInterface $review,Request $request)
    {
        if (isDemoServer()):
            $response['message']    = __('This function is disabled in demo server.');
            $response['title']      = __('Ops..!');
            $response['status']     = 'error';
            return response()->json($response);
        endif;

        if ($review->statusChange($request['data'])):
            $response['message'] = __('Updated Successfully');
            $response['title'] = __('Success');
            $response['status'] = 'success';
            return response()->json($response);
        else:
            $response['message'] = __('Something went wrong, please try again');
            $response['title'] = __('Ops..!');
            $response['status'] = 'error';
            return response()->json($response);
        endif;
    }

    public function productImport(Request $request)
    {
        return view('admin.products.products.import-products');
    }
}
