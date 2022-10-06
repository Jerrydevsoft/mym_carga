<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtractionBrandModel extends Model
{
    use HasFactory;
    protected $table = 'extraction_brand';
    public $timestamps = false;
    protected $fillable = ['code', 'name','lengthName','status','is_active','is_deleted'];
}
