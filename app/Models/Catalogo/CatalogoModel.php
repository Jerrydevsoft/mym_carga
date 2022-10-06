<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CatalogoModel extends Model
{
    use HasFactory;
    
    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'pgsql';

    /**
    * The database table used by the model.
    *
    * @var string
    */
    // protected $table = 'wi_description';
    protected $table = 'impcust.wi_description';

    public $timestamps = false;
}
