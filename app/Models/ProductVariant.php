<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = ["store_id", "product_id", "variant_id", "title", "sku", "position", "fulfillment_service", "option1", "option2", "option3"];
}
