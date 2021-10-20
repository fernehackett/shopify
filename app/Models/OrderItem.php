<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OrderItem extends Model
{
	use SoftDeletes;
    public $timestamps = false;
	protected $dates = ["deleted_at"];
	protected $fillable = ["store_id","order_id","item_id","fulfillable_quantity","fulfillment_status","product_id","quantity",
		"sku","title","variant_id","variant_title","vendor","properties","order_number"];
	protected $casts = [
		"properties"=>"object"
	];

	public function product(){
		return $this->belongsTo(Product::class,"product_id","product_id");
	}
}
