<?php

namespace App\Jobs;

use App\Models\Store;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

class ShopifyCreateWebhooks implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
	protected $store;

	/**
	 * Create a new job instance.
	 *
	 * @param ShopifySDK $shopify
	 */
    public function __construct(Store $store)
    {
        $this->store = $store;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
	    $store = $this->store;
	    $config = [
		    'ApiVersion'  => '2021-10',
		    'ShopUrl'     => $store->shopify_url,
		    "AccessToken" => $store->access_token
	    ];
	    $shopify = new ShopifySDK($config);
	    $WebHook = $shopify->Webhook;
	    $webhooks = $WebHook->get();
	    foreach ($webhooks as $webhook){
		    $shopify->Webhook($webhook["id"])->delete();
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "app/uninstalled",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    };
	    try {
		    $WebHook->post([
			    "topic"   => "products/create",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    };
	    try {
		    $WebHook->post([
			    "topic"   => "products/delete",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    };
	    try {
		    $WebHook->post([
			    "topic"   => "products/update",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "orders/create",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "orders/delete",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "orders/fulfilled",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "orders/paid",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "orders/updated",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "orders/partially_fulfilled",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "fulfillments/create",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
	    try {
		    $WebHook->post([
			    "topic"   => "fulfillments/update",
			    "address" => route("api.webHook"),
			    "format"  => "json"
		    ]);
	    } catch (ApiException $e) {
		    \Log::warning($e);
	    }
    }
}
