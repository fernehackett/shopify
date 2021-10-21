<?php

Route::get("dashboard", 'DashboardController@index')->name("dashboard");
Route::post("anti-theft", 'DashboardController@antiTheft')->name("anti-theft");