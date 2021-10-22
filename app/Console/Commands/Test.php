<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
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
        $access_token = "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";
        $shop_url = "storetestexp.myshopify.com";
        $config = [
            'ShopUrl'      => $shop_url,
            'ApiKey'       => $api_key,
            'SharedSecret' => $api_secret,
            "AccessToken"  => $access_token
        ];
        $shopify = new ShopifySDK($config);
        $res = $shopify->Webhook->post([
            "topic"   => "app/uninstalled",
            "address" => route("api.webHook"),
            "format"  => "json"
        ]);
        dump($res);
        return 0;
    }
}
