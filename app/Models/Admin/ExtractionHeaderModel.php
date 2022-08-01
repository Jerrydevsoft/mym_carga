<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtractionHeaderModel extends Model
{
    use HasFactory;
    protected $table = 'extraction_header';
    public $timestamps = false;
}
