<?php
Route::group(['middleware'=>["verify.shopify"]], function () {
    Route::get('/', "Shopify\DashboardController@index")->name("home");
});
Route::group(["as"=>"shopify.","namespace"=>"Shopify"], function(){
    Route::post("anti-theft", 'DashboardController@antiTheft')->name("anti-theft");
});

Route::get('policy', 'HomeController@policy')->name("policy");
