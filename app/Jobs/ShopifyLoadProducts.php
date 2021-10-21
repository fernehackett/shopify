<?php

namespace App\Jobs;

use App\Models\Product;
use App\Models\Store;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPShopify\ShopifySDK;

class ShopifyLoadProducts implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $store, $params;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Store $store, $params = null)
    {
	    $this->store = $store;
	    $this->params = $params;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $store = $this->store;
	    $params = $this->params;
	    if(!$params) $params = ["limit"=>250];
	    $config = [
		    'ApiVersion'  => '2020-07',
		    'ShopUrl'     => $store->shopify_url,
		    "AccessToken" => $store->access_token
	    ];
	    $shopify = new ShopifySDK($config);
	    $shopifyProduct = $shopify->Product;
	    $products = $shopifyProduct->get($params);
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
			    'created_at'   => Carbon::parse($product['created_at']),
			    'updated_at'   => Carbon::parse($product['updated_at'])
		    ];
		    Product::updateOrCreate([
			    "store_id"   => $store->id,
			    "product_id" => $product['id']
		    ], $filter);
	    }
	    $params = $shopifyProduct->getNextPageParams();
	    if(count($params) > 0)
		    self::dispatch($store, $params);
    }
}
