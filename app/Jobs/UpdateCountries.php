<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class UpdateCountries implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    private $urlBase;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->urlBase = 'http://192.168.1.190:81/api/';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        /* :::: actualizamos las marcas :::: */
        $lstBrand = json_decode(file_get_contents($this->urlBase .'ecommerce/getTrademarks'));
        if (count($lstBrand) > 0) {
            /* :::. recorremos la lista de marcas para ordenarlas posteriormente :::  */
            foreach ($lstBrand as $b => $br) {
                $existBrand = DB::table('mst_product_brand')->whereRaw('TRIM(code) = "'.trim($br->code).'"')->get();
                if (count($existBrand)>0) {
                    //print_r("cantidad: ".count($existBrand)." marca:".$br->name." - Ya existente\n" );
                }else {
                    $data=array('code'=>trim($br->code),"name"=>trim($br->name),"typeSearchBrand"=>trim($br->name),"lengthName"=>strlen(trim($br->name)),"idRefence"=>$br->id);
                    DB::table('mst_product_brand')->insert($data);
                }
            }
        }
    }

    function object_sorter($clave,$orden=null) {
        return function ($a, $b) use ($clave,$orden) {
              $result=  ($orden=="DESC") ? strnatcmp($b->$clave, $a->$clave) :  strnatcmp($a->$clave, $b->$clave);
              return $result;
        };
    }
}
