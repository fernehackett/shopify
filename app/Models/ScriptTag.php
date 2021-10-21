<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScriptTag extends Model
{
    use HasFactory;

    protected $fillable = ["shopify_url", "name", "script_id", "event", "cache", "display_scope", "src"];
}
