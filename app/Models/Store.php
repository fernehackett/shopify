<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
	use SoftDeletes;
	protected $dates = ["deleted_at"];
	protected $fillable = ["name","shopify_url","domain","access_token","status", "owner_id"];

}
