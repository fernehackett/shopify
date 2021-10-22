<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Models\ScriptTag;
use App\Models\Store;
use Illuminate\Http\Request;
use PHPShopify\ShopifySDK;

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
        $token = session("shopify_token", null);
        $store_url = session("store_url", "");
        $store = Store::where("shopify_url", $store_url)->first();
        $antiTheft = ScriptTag::where("shopify_url", $store_url)->where("name", "anti-theft")->first();
        return response()->view("shopify.dashboard.index", ["store" => $store, "token" => $token, "antiTheft" => $antiTheft])->header("token", $token);
    }

    public function antiTheft(Request $request)
    {
        $store_url = session("store_url", "");
        $store = Store::where("shopify_url", $store_url)->first();
        $config = [
            'ApiVersion'  => '2020-07',
            'ShopUrl'     => $store_url,
            "AccessToken" => $store->access_token
        ];
        $shopify = new ShopifySDK($config);
        if ($request->has("anti-theft") && $request->get("anti-theft") == "on") {
            try {
                $data = [
                    "event"         => "onload",
                    "src"           => asset("/js/anti-theft.min.js"),
                    "display_scope" => "online_store",
                ];
                $response = $shopify->ScriptTag()->post($data);
                $response["shopify_url"] = $store_url;
                $response["name"] = "anti-theft";
                $response["script_id"] = $response["id"];
                unset($response["id"]);
                ScriptTag::create($response);
                return back()->withSuccess("Update successfully!");
            } catch (\Exception $e) {
                return back()->withSuccess("Update failed!");
            }
        } else {
            $script_id = $request->get("script_id");
            $shopify->ScriptTag($script_id)->delete();
            ScriptTag::where("script_id", $script_id)->delete();
            return back()->withSuccess("Update successfully!");
        }
    }
}
