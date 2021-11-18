<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use PHPUnit\Util\Exception;

class StoreController extends Controller
{
    protected $api_key, $api_secret, $scope;

    public function __construct()
    {
        $this->api_key = env("SHOPIFY_API_KEY", "9980cee5ec9b979c47ef231b98d4330a");
        $this->api_secret = env("SHOPIFY_SECRET", "84d73168d39f6b575407c3b3e8249ae2");
        $this->scope = env("SHOPIFY_SCOPE", "read_products,read_orders,write_orders,write_script_tags,read_script_tags,write_products");
    }

    public function install(Request $request)
    {
        $api_key = $this->api_key;
        $api_secret = $this->api_secret;
        $scope = $this->scope;
        $shop_url = $request->get("shop");
        $config = [
            'ShopUrl'      => $shop_url,
            'ApiKey'       => $api_key,
            'SharedSecret' => $api_secret,
        ];
        $accessToken = \PHPShopify\AuthHelper::createAuthRequest($scope);
        $shopify = new ShopifySDK($config);
        $shop = $shopify->Shop->get();
        $filter = [
            'name'         => $shop['name'],
            'shopify_url'  => $config['ShopUrl'],
            'domain'       => $shop['domain'],
            'access_token' => $accessToken
        ];
        $store = Store::updateOrCreate(["shopify_url" => $config['ShopUrl']], $filter);
        try {
            $shopify->Webhook->post([
                "topic"   => "app/uninstalled",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
            \Log::alert($e->getMessage());
        };
        try {
            $shopify->Webhook->post([
                "topic"   => "products/create",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        };
        try {
            $shopify->Webhook->post([
                "topic"   => "products/delete",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        };
        try {
            $shopify->Webhook->post([
                "topic"   => "products/update",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {

        }
        try {
            $shopify->Webhook->post([
                "topic"   => "orders/create",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        }
        try {
            $shopify->Webhook->post([
                "topic"   => "orders/delete",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        }
        try {
            $shopify->Webhook->post([
                "topic"   => "orders/fulfilled",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        }
        try {
            $shopify->Webhook->post([
                "topic"   => "orders/paid",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        }
        try {
            $shopify->Webhook->post([
                "topic"   => "orders/updated",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        }
        try {
            $shopify->Webhook->post([
                "topic"   => "orders/partially_fulfilled",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (Exception $e) {
        }

        $count = $shopify->Product->count();
        $allproducts = [];
        if ($count > 0) {
            $times = ceil($count / 250);
            for ($i = 1; $i <= $times; $i++) {
                $products = $shopify->Product->get(['limit' => 250, 'page' => $i]);
                foreach ($products as $product) {
                    $filter = [
                        'title'        => $product['title'],
                        'product_id'   => $product['id'],
                        'link'         => 'https://' . $store->shopify_url . '/products/' . $product['handle'],
                        'banner'       => !empty($product['image']['src']) ? $product['image']['src'] : '',
                        'vendor'       => $product['vendor'],
                        'product_type' => $product['product_type'],
                        'tags'         => $product['tags'],
                        'description'  => $product['body_html'],
                        'store_id'     => $store->id,
                        'created_at'   => date('Y-m-d H:i:s', strtotime($product['created_at'])),
                        'updated_at'   => date('Y-m-d H:i:s', strtotime($product['updated_at']))
                    ];
                    Product::updateOrCreate([
                        "store_id"   => $store->id,
                        "product_id" => $product['id']
                    ], $filter);
                }
            }
        }
        return redirect("http://{$config['ShopUrl']}/admin/apps");
    }

    public function webHook(Request $request)
    {
        try {
            $topic = $request->server('HTTP_X_SHOPIFY_TOPIC', "");
            $data = file_get_contents('php://input');
            switch ($topic) {
                case 'products/update':
                case 'products/create':
                case 'collections/update':
                case 'collections/create':
                case 'collections/delete':
                case 'orders/create':
                case 'orders/updated':
                case 'orders/fulfilled':
                case 'orders/paid':
                case 'orders/partially_fulfilled':
                case 'orders/delete':
                case "app/uninstalled":
                    $store = json_decode($data, true);
                    $store_url = $store["myshopify_domain"];
                    User::where("name", $store_url)->forceDelete();
                    break;
                default:
                    \Log::info($topic);
                    \Log::info(json_decode($data, true));
                    break;
            }
            return response()->json(["status" => "succeed"]);
        }catch(\Exception $ex){
            \Log::error($ex->getMessage());
            return response()->json(["status" => "failed"]);
        }
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
