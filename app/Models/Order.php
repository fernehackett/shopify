<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Order extends Model
{
	use SoftDeletes;

	public $timestamps = false;
	protected $dates = ["deleted_at"];
	protected $fillable = ["store_id", "order_id", "order_number", "customer", "financial_status", "fulfillment_status", "name", "note", "note_attributes", "payment_details", "phone",
		"shipping_address", "token", "total_price", "order_status_url", "shipping_lines", "created_at"];
	protected $casts = [
		"customer"         => "object",
		"note_attributes"  => "object",
		"payment_details"  => "object",
		"shipping_address" => "object",
		"shipping_lines"   => "object",
	];
	public function store()
	{
		return $this->belongsTo(Store::class);
	}

	public function orderItems(){
		return $this->hasMany(OrderItem::class,"order_id","order_id");
	}

//	public function transactions(){
//		return $this->hasMany(Transaction::class,"order_id","order_id");
//	}
//
//	public function fulfillments(){
//		return $this->hasMany(Fulfillment::class,"order_id","order_id");
//	}
}
