<?php

namespace App\Console\Commands;

use App\Models\Store;
use Illuminate\Console\Command;
use PHPShopify\Exception\ApiException;
use PHPShopify\ShopifySDK;

class Test extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $api_key = env("SHOPIFY_API_KEY", "73fdeda4d7dddca79264af93a10ce9f0");
        $api_secret = env("SHOPIFY_SECRET", "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");
        $shop_url = "storetestexp.myshopify.com";

        $store = Store::where("shopify_url", $shop_url)->first();
        $access_token = $store->access_token;
        $config = [
            'ShopUrl'      => $shop_url,
            'ApiKey'       => $api_key,
            'SharedSecret' => $api_secret,
            "AccessToken"  => $access_token
        ];
        $shopify = new ShopifySDK($config);
        try {
            $shopify->Webhook->post([
                "topic"   => "customers/data_request",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (ApiException $e) {
            dump($e->getMessage());
        };
        try {
            $shopify->Webhook->post([
                "topic"   => "customers/redact",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (ApiException $e) {
            dump($e->getMessage());
        };
        try {
            $shopify->Webhook->post([
                "topic"   => "shop/redact",
                "address" => route("api.webHook"),
                "format"  => "json"
            ]);
        } catch (ApiException $e) {
            dump($e->getMessage());
        };
        return 0;
    }
}
