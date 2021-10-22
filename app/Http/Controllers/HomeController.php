<?php

namespace App\Http\Controllers;

use App\Jobs\ShopifyCreateWebhooks;
use App\Jobs\ShopifyLoadProducts;
use App\Models\Store;
use Illuminate\Http\Request;
use PHPShopify\ShopifySDK;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
//        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        return view('home');
    }
    public function install()
    {
        return view('install');
    }

    public function submit(Request $request)
    {
        $api_key = env("SHOPIFY_API_KEY", "");
        $api_secret = env("SHOPIFY_SECRET", "");
        $scope = env("SHOPIFY_SCOPE", "read_products,read_orders,write_orders,write_script_tags,read_script_tags,write_products");
        $shop_url = $request->get("shop");
        $config = [
            'ApiVersion'   => '2020-07',
            'ShopUrl'      => $shop_url,
            'ApiKey'       => $api_key,
            'SharedSecret' => $api_secret,
        ];
        $redirectUrl = route("success");
        $shopify = new ShopifySDK($config);
        return \PHPShopify\AuthHelper::createAuthRequest($scope, $redirectUrl);
    }

    public function success(Request $request)
    {
        $api_key = env("SHOPIFY_API_KEY", "");
        $api_secret = env("SHOPIFY_SECRET", "");
        $scope = env("SHOPIFY_SCOPE", "read_products,read_orders,write_orders,write_script_tags,read_script_tags,write_products");
        $shop_url = $request->get("shop");
        $config = [
            'ApiVersion'   => '2020-07',
            'ShopUrl'     => $shop_url,
            'ApiKey'       => $api_key,
            'SharedSecret' => $api_secret,
        ];
        \PHPShopify\ShopifySDK::config($config);
        $accessToken = \PHPShopify\AuthHelper::getAccessToken();
        $config['AccessToken'] = $accessToken;
        $shopify = new ShopifySDK($config);
        $shop = $shopify->Shop->get();
        $filter = [
            'name'         => $shop['name'],
            'shopify_url'  => $config['ShopUrl'],
            'domain'       => $shop['domain'],
            'access_token' => $accessToken
        ];
        $store = Store::updateOrCreate(["shopify_url" => $config['ShopUrl']], $filter);
        ShopifyCreateWebhooks::dispatch($store);
//        ShopifyLoadProducts::dispatch($store);
        return redirect("http://{$config['ShopUrl']}/admin/apps");
    }
}
