<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalogo\CatalogoModel;
use Illuminate\Support\Facades\DB;

class CatalogoController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        
    }

    public function showVin(){
        $data = CatalogoModel::select('*')->limit(10)->get();
        // $data = DB::connection('pgsql')->table('wi_description')->select('*')->limit(10)->get();
        return view('catalogo.vin.list', compact('data','data'));
    }
}
