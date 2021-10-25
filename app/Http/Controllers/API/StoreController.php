<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use PHPShopify\ShopifySDK;
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
            $shop = $request->server('HTTP_X_SHOPIFY_SHOP_DOMAIN', "");
            $api_secret = $this->api_secret;
            $store = Store::where("shopify_url", $shop)->first();
            if (!$store) return response()->json(["status" => "succeed"]);
            $hmac_header = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');
            $data = file_get_contents('php://input');
            \Log::info($data);
            $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $api_secret, true));
            if (!hash_equals($hmac_header, $calculated_hmac)) {
                \Log::error("Cannot verify hmac");
                return response()->json(["status" => "succeed"]);
            }
//		\Log::warning($topic);
            switch ($topic) {
                case 'products/update':
                case 'products/create':
                    $product = json_decode($data, true);
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
                        'created_at'   => Carbon::parse($product["created_at"]),
                        'updated_at'   => Carbon::parse($product["updated_at"])
                    ];
                    Product::updateOrCreate([
                        'product_id' => $product['id'],
                        'store_id'   => $store->id,
                    ], $filter);
                    $variants = $product["variants"];
                    foreach ($variants as $variant) {
                        $variant["variant_id"] = $variant["id"];
                        $variant["store_id"] = $store->id;
                        ProductVariant::updateOrCreate(["variant_id" => $variant["variant_id"]], $variant);
                    }
                    break;
                case 'products/delete':
                    $product = json_decode($data, true);
                    Product::where("store_id", $store->id)->where("product_id", $product["id"])->delete();
                    break;
                case 'collections/update':
                case 'collections/create':
                case 'collections/delete':
                    $collection = json_decode($data, true);
                    \Log::info($collection);
                    break;
                case 'orders/create':
                case 'orders/updated':
                case 'orders/fulfilled':
                case 'orders/paid':
                case 'orders/partially_fulfilled':
                    try {
                        $item = json_decode($data, true);
                        $data = [
                            "store_id"           => $store->id,
                            "order_id"           => $item["id"],
                            "order_number"       => $item["number"],
                            "customer"           => isset($item["customer"]) ? $item["customer"] : null,
                            "financial_status"   => $item["financial_status"],
                            "fulfillment_status" => $item["fulfillment_status"],
                            "name"               => $item["name"],
                            "note"               => $item["note"],
                            "note_attributes"    => $item["note_attributes"],
                            "payment_details"    => isset($item["payment_details"]) ? $item["payment_details"] : null,
                            "phone"              => $item["phone"],
                            "shipping_address"   => isset($item["shipping_address"]) ? $item["shipping_address"] : null,
                            "token"              => $item["token"],
                            "total_price"        => $item["total_price"],
                            "order_status_url"   => $item["order_status_url"],
                            "shipping_lines"     => $item["shipping_lines"],
                            "created_at"         => Carbon::parse($item["created_at"]),
                        ];
                        $order = Order::updateOrCreate([
                            "store_id" => $store->id,
                            "order_id" => $item["id"],
                        ], $data);
                        foreach ($item["line_items"] as $line_item) {
                            $line = [
                                "store_id"             => $store->id,
                                "order_id"             => $order->order_id,
                                "item_id"              => $line_item["id"],
                                "fulfillable_quantity" => $line_item["fulfillable_quantity"],
                                "fulfillment_status"   => $line_item["fulfillment_status"],
                                "product_id"           => $line_item["product_id"],
                                "quantity"             => $line_item["quantity"],
                                "sku"                  => $line_item["sku"],
                                "title"                => $line_item["title"],
                                "variant_id"           => $line_item["variant_id"],
                                "variant_title"        => $line_item["variant_title"],
                                "vendor"               => $line_item["vendor"],
                                "properties"           => $line_item["properties"],
                                "order_number"         => $order->order_number,
                            ];
                            OrderItem::updateOrCreate([
                                "store_id" => $store->id,
                                "order_id" => $order->order_id,
                                "item_id"  => $line_item["id"],
                            ], $line);
                        }
                    } catch (Exception $ex) {
                        \Log::error($ex->getMessage());
                    }
                    break;
                case 'orders/delete':
                    $item = json_decode($data, true);
                    $order = Order::where("order_id", $item["id"])->first();
                    if ($order) $order->delete();
                    break;
                case "app/uninstalled":
                    $store = json_decode($data, true);
                    $store_url = $store["myshopify_domain"];
                    Store::where("shopify_url", $store_url)->delete();
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
