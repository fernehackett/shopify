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
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
//        dump($request->query);
        dump($request->query('session'));
        dump(session($request->query('session')));
        $api_secret = env("SHOPIFY_SECRET", "");
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
            $store_url = $request->get("shop", session("store_url"));
            if(!isset($store_url)){
                \Log::alert("Can get store url!");
                abort(403);
            }
            $store = Store::where("shopify_url", $store_url)->first();
            $token = [
                "hmac"            => $hmac,
                "calculated_hmac" => $calculated_hmac,
                "store_url"       => $store_url,
            ];
            session($token);
            session("shopify_token", base64_encode(json_encode($token)));
        } else {
            $hmac = session("hmac");
            $calculated_hmac = session("calculated_hmac");
            $store_url = session("store_url");
            $store = Store::where("shopify_url", $store_url)->first();
        }
        if (!isset($store)) {
//            return redirect(route("submit", ["shop" => $store_url]));
        }
        if (!isset($hmac) || !hash_equals($hmac, $calculated_hmac)) {
            \Log::alert("{$store_url}--{$hmac}--{$calculated_hmac}");
//            return redirect()->to($store_url."/admin/apps/".env("SHOPIFY_SLUG",""));
        }
        return $next($request);
    }
}
