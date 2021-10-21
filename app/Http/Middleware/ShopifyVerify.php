<?php

namespace App\Http\Middleware;

use App\Models\Store;
use Closure;
use Illuminate\Http\Request;

class ShopifyVerify
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $api_secret = env("SHOPIFY_SECRET", "84d73168d39f6b575407c3b3e8249ae2");
        if (strtolower($request->method()) == "get") {
            $hmac = $request->get("hmac");
            if (isset($hmac)) {
                $data = $request->all();
                unset($data["hmac"]);
                $temp = [];
                foreach ($data as $key => $value) {
                    $key = str_replace("%", "%25", $key);
                    $key = str_replace("&", "%26", $key);
                    $key = str_replace("=", "%3D", $key);
                    $value = str_replace("%", "%25", $value);
                    $value = str_replace("&", "%26", $value);
                    $temp[] = "{$key}={$value}";
                }
                $str = implode("&", $temp);
                $calculated_hmac = hash_hmac('sha256', $str, $api_secret);
                $store_url = $request->get("shop", "");
                $store = Store::where("shopify_url", $store_url)->first();
//                $owner = $store->owner;
//                auth()->login($owner );
                $token = [
                    "hmac"            => $hmac,
                    "calculated_hmac" => $calculated_hmac,
                    "store_url"       => $store_url,
                ];
                session($token);
                session("shopify_token", base64_encode(json_encode($token)));
            } else {
                $hmac = session("hmac", null);
                $calculated_hmac = session("calculated_hmac", null);
            }
            if (!isset($hmac) || !hash_equals($hmac, $calculated_hmac)) {
                abort(403);
            }
        } elseif (strtolower($request->method()) == "post") {
            $hmac = $request->server('HTTP_X_SHOPIFY_HMAC_SHA256');
            $data = file_get_contents('php://input');
            $calculated_hmac = base64_encode(hash_hmac('sha256', $data, $api_secret, true));
            if(!isset($hmac)){
                $hmac = session("hmac", null);
                $calculated_hmac = session("calculated_hmac", null);
            }
            if (!isset($hmac) || !hash_equals($hmac, $calculated_hmac)) {
                abort(403);
            }
            $store_url = $request->server('HTTP_X_SHOPIFY_SHOP_DOMAIN', session("store_url", null));
            $store = Store::where("shopify_url", $store_url)->first();
//            $owner = $store->owner;
//            auth()->login($owner );
            $token = [
                "hmac"            => $hmac,
                "calculated_hmac" => $calculated_hmac,
                "store_url"       => $store_url,
            ];
            session($token);
            session("shopify_token", base64_encode(json_encode($token)));
        }
        return $next($request);
    }
}
