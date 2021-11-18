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
