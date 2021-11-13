<?php

namespace App\Http\Controllers\Shopify;

use App\Http\Controllers\Controller;
use App\Models\ScriptTag;
use Illuminate\Http\Request;

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
        $user = auth()->user();
        $antiTheft = ScriptTag::where("shopify_url", $user->name)->where("name", "anti-theft")->first();
        return response()->view("shopify.dashboard.index", ["antiTheft" => $antiTheft]);
    }

    public function antiTheft(Request $request)
    {
        $user = auth()->user();
        $store_url = $user->name;
        if ($request->has("anti-theft") && $request->get("anti-theft") == "on") {
            try {
                $data = [
                    "script_tag" => [
                        "event"         => "onload",
                        "src"           => asset("/js/anti-theft.min.js"),
                        "display_scope" => "online_store",
                    ]
                ];
                $response = $user->api()->rest('POST', "/admin/api/script_tags.json", $data);
                $scriptTag = ((array)$response["body"]["script_tag"])["container"];
                $scriptTag["shopify_url"] = $store_url;
                $scriptTag["name"] = "anti-theft";
                $scriptTag["script_id"] = $scriptTag["id"];
                unset($scriptTag["id"]);
                ScriptTag::create($scriptTag);
                return redirect(route("home", ["success" => "Enable successfully!"]));
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
                return redirect(route("home", ["error" => "Enable Failed!"]));
            }
        } else {
            $script_id = $request->get("script_id");
            try {
                $user->api()->rest('DELETE', "/admin/api/script_tags/{$script_id}.json");
            } catch (\Exception $ex) {
                \Log::error($ex->getMessage());
            }
            ScriptTag::where("script_id", $script_id)->delete();
            return redirect(route("home", ["success" => "Disable successfully!"]));
        }
    }

    public function getAntiTheftFile(Request $request)
    {
        $store_url = $request->get("shop", "");
        $antiTheft = ScriptTag::where("shopify_url", $store_url)->where("name", "anti-theft")->first();
        if ($antiTheft) {
            return view("shopify.scripts.anti-theft");
        } else {
            return "";
        }
    }
}
