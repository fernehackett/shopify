<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{

	use SoftDeletes;

	protected $dates = ["deleted_at"];
	protected $fillable = ["store_id","product_id","title","link","short_link","banner","product_type","vendor","tags","description","status","fbpixel"];

	public function store()
	{
		return $this->belongsTo(Store::class);
	}
}
