<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    protected $api_key, $api_secret, $scope;
    public function __construct()
    {
        $this->api_key = env("SHOPIFY_API_KEY", "9980cee5ec9b979c47ef231b98d4330a");
        $this->api_secret = env("SHOPIFY_SECRET", "84d73168d39f6b575407c3b3e8249ae2");
        $this->scope = env("SHOPIFY_SCOPE", "read_products,read_orders,write_orders,write_script_tags,read_script_tags,write_products");

    }

    public function index(Request $request)
    {
        $shop = $request->get("shop");
        return view("shopify.dashboard.index", compact("shop"));
    }

    public function antiTheft(Request $request)
    {

    }
}
