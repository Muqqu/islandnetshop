<?php
namespace App\Repositories\Admin;

use App\Models\Campaign;
use App\Models\CampaignProduct;
use App\Models\Product;
use App\Models\ProductStock;
use App\Models\WholesalePrice;
use App\Repositories\Interfaces\Admin\Product\ProductInterface;
use App\Repositories\Interfaces\Admin\Product\ProductLanguageInterface;
use App\Repositories\Interfaces\Admin\WholesaleProductInterface;
use App\Traits\ImageTrait;
use App\Traits\RandomStringTrait;
use App\Traits\SlugTrait;
use Carbon\Carbon;
use Sentinel;
use Illuminate\Support\Facades\DB;

class WholesaleProductRepository implements WholesaleProductInterface

{

}
