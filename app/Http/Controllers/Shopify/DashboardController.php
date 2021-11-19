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
                return redirect()->to(route("home", ["notice" => "Enable successfully!"]));
            } catch (\Exception $e) {
                \Log::error($e->getMessage());
                return redirect()->to(route("home", ["error" => "Enable Failed!"]));
            }
        } else {
            $script_id = $request->get("script_id");
            try {
                $user->api()->rest('DELETE', "/admin/api/script_tags/{$script_id}.json");
            } catch (\Exception $ex) {
                \Log::error($ex->getMessage());
                return redirect()->to(route("home", ["error" => "Disable successfully!"]));
            }
            ScriptTag::where("script_id", $script_id)->delete();
            return redirect()->to(route("home", ["notice" => "Disable successfully!"]));
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
