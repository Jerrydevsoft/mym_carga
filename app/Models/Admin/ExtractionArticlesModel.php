<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtractionArticlesModel extends Model
{
    use HasFactory;
    protected $table = 'extraction_brand_article';
    public $timestamps = false;
    protected $fillable = ['brandId','code', 'name','factory_code','status','is_active','is_deleted'];
}
