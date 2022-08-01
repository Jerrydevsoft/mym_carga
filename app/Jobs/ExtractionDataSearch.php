<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Admin\ExtractionHeaderModel;
use App\Models\Admin\ExtractionModel;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ExtractionDataSearch implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $extractionHeader;
    protected $urlBase;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(ExtractionHeaderModel $extractionHeader)
    {
        $this->extractionHeader = $extractionHeader;
        $this->urlBase = 'http://192.168.1.190:81/api/';
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("::::::::::::: regularizar_campos_faltantes  :::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        if ($this->extractionHeader->id > 0) {
            //$response = Http::acceptJson()->get('http://192.168.1.190:81/api/ecommerce/getTrademarks');
            $lstBrand = json_decode(file_get_contents($this->urlBase .'ecommerce/getTrademarks'));
            //var_dump($response->getBody()->getContents());
            //obtenemos la lista de los productos y marca
            //$lstBrand = DB::table('mst_product_brand')->get();
            if (count($lstBrand) > 0) {
                /* :::. recorremos la lista de marcas para ordenarlas posteriormente :::  */
                foreach ($lstBrand as $b => $br) {
                    $br->length = strlen(trim($br->name));
                }
                //ordenamos por medio de la funcion object_sorter
                usort($lstBrand, $this->object_sorter('length','DESC'));
                foreach ($lstBrand as $d => $brand) {
                    if (strlen(trim($brand->name))> 0) {
                        $lstFound1 = DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('status','CHARGED')
                                    ->whereRaw('UPPER("descripcionComercial") like "%'.strtoupper(trim($brand->name)).'%"')
                                    ->get();
                        //print_r(":::::::::::::: lstFound1::::::::::::\n" );
                        if (count($lstFound1) > 0) {
                            print_r(":::::::::::::: RECORREMOS EL lstFound1::::::::::::\n" );
                            print_r(":::::: busqueda data : ".count($lstFound1)."::::::::::::\n" );
                            print_r("::: id: " .$brand->id. " brand name : ".$brand->name."::::::::::::\n" );
                            foreach ($lstFound1 as $f1 => $found1) {
                                print_r(":::::: DATA ENCONTRADA ID: ".$found1->id." indice: ".$f1." ::::::::::\n" );
                                DB::table('extraction_subida')->where('id', $found1->id)
                                    ->update(array('status' => 'FOUNDED', 'marca' => $brand->code, 'codigo' => $brand->name, 'typeFoundColor' => 'badge bg-success'));
                            }
                        }else{
                            //print_r(":::::::::::::: ELSE LSTFOUND1::::::::::::\n" );
                            // quitamos lo que le sigue al ultimo espacio de la marca
                            print_r(":::::::::::::: BRAND :   ".$brand->name." ::::::::::::\n" );
                            $txtNotSpaceIni = substr($brand->name, 0, strpos(trim($brand->name), " "));
                            print_r(":::::::::::::: BRAND SIN ESPACIO LEFT :   ".$txtNotSpaceIni." ::::::::::::\n" );
                            $txtNotSpaceFinal = substr($brand->name, strpos(trim($brand->name), " ", strlen(trim($brand->name))));
                            print_r(":::::::::::::: BRAND SIN ESPACIO RIGHT :   ".$txtNotSpaceFinal." ::::::::::::\n" );
                            if (strlen(trim($txtNotSpaceIni)) > 4) {
                                //print_r(":::::::::::::: ELSE LSTFOUND1 txtNotSpace|IF::::::::::::\n" );
                                $lstFoundNotSpaceIni = DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                        ->where('descripcionComercial','like', '%' . $txtNotSpaceIni . '%')
                                        ->whereIn('status',['CHARGED','PARTIAL_FOUND'])
                                        ->get();
                                if (count($lstFoundNotSpaceIni) > 0) {
                                    print_r("::: id: " .$brand->id. " brand name : ".$brand->name."::::::::::::\n" );
                                    print_r("::::: txtNotSpaceIni: " .$txtNotSpaceIni. "::::::::::::\n" );
                                    foreach ($lstFoundNotSpaceIni as $f => $found) {
                                        DB::table('extraction_subida')
                                            ->where('id', $found->id)
                                            ->update(array('status' => 'PARTIAL_FOUND', 'marca' => $brand->code, 'codigo' => $brand->name, 'typeFoundColor' => 'badge bg-warning'));
                                    }
                                }
                            }else{
                                if (strlen(trim($txtNotSpaceFinal)) > 4) {
                                    //print_r(":::::::::::::: ELSE LSTFOUND1 txtNotSpace|IF::::::::::::\n" );
                                    $lstFoundNotSpaceFinal = DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('descripcionComercial','like', '%' . $txtNotSpaceFinal . '%')
                                    ->whereIn('status',['CHARGED','PARTIAL_FOUND'])
                                    ->get();
                                    if (count($lstFoundNotSpaceFinal) > 0) {
                                        print_r("::: id: " .$brand->id. " brand name : ".$brand->name."::::::::::::\n" );
                                        print_r("::::: txtNotSpaceFinal: " .$txtNotSpaceFinal. "::::::::::::\n" );
                                        foreach ($lstFoundNotSpaceFinal as $f => $found) {
                                            DB::table('extraction_subida')
                                                ->where('id', $found->id)
                                                ->update(array('status' => 'PARTIAL_FOUND', 'marca' => $brand->code, 'codigo' => $brand->name, 'typeFoundColor' => 'badge bg-warning'));
                                        }
                                    }
                                }
                            }
                            print_r(":::::::::::::: BRAND :   ".$brand->name." ::::::::::::\n" );
                            $txtNotGuionIni = substr($brand->name, 0, strpos(trim($brand->name), "-"));
                            print_r(":::::::::::::: BRAND SIN ESPACIO LEFT :   ".$txtNotSpaceIni." ::::::::::::\n" );
                            $txtNotGuionFinal = substr($brand->name, strpos(trim($brand->name), "-", strlen(trim($brand->name))));
                            print_r(":::::::::::::: BRAND SIN ESPACIO RIGHT :   ".$txtNotSpaceFinal." ::::::::::::\n" );
                            if(strlen($txtNotGuionIni)> 4){
                                //print_r(":::::::::::::: ELSE LSTFOUND1 txtNotGuion|IF::::::::::::\n" );
                                $lstFoundNotGuionIni = DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                        ->where('descripcionComercial','like', '%' . $txtNotGuionIni . '%')
                                        ->where('status','CHARGED')
                                        ->get();
                                if (count($lstFoundNotGuionIni) > 0) {
                                    print_r("::: id: " .$brand->id. " brand name : ".$brand->name."::::::::::::\n" );
                                    print_r("::::: txtNotGuionIni: " .$txtNotGuionIni. "::::::::::::\n" );
                                    foreach ($lstFoundNotGuionIni as $f2 => $found2) {
                                        DB::table('extraction_subida')
                                            ->where('id', $found2->id)
                                            ->update(array('status' => 'PARTIAL_FOUND', 'marca' => $brand->code, 'codigo' => $brand->name,'typeFoundColor' => 'badge bg-warning'));
                                    }
                                }
                            }else{
                                if (strlen(trim($txtNotGuionFinal)) > 4) {
                                    //print_r(":::::::::::::: ELSE LSTFOUND1 txtNotSpace|IF::::::::::::\n" );
                                    $lstFoundNotGuionFinal = DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                    ->where('descripcionComercial','like', '%' . $txtNotGuionFinal . '%')
                                    ->whereIn('status',['CHARGED','PARTIAL_FOUND'])
                                    ->get();
                                    if (count($lstFoundNotGuionFinal) > 0) {
                                        print_r("::: id: " .$brand->id. " brand name : ".$brand->name."::::::::::::\n" );
                                        print_r("::::: txtNotGuionFinal: " .$txtNotGuionFinal. "::::::::::::\n" );
                                        foreach ($lstFoundNotGuionFinal as $f => $found) {
                                            DB::table('extraction_subida')
                                                ->where('id', $found->id)
                                                ->update(array('status' => 'PARTIAL_FOUND', 'marca' => $brand->code, 'codigo' => $brand->name, 'typeFoundColor' => 'badge bg-warning'));
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                //actualizamos los registros que no se pudieron encontrar
                print_r(":::::::::::::: ACTUALIZAMOS LOS REGISTROS NO ENCONTRADOS::::::::::::\n" );
                DB::table('extraction_subida')->where('extractionHeaderId', $this->extractionHeader->id)
                                            ->where('status', 'CHARGED')
                                            ->update(array('status' => 'NOT FOUND', 'typeFoundColor' => 'badge bg-danger'));
            }
            /*
            if (count($lstDetail) > 0) {
                print_r(":::::::::::::: ENTRO AL SEGUNDO IF::::::::::::\n" );
                foreach ($lstDetail as $d => $detail) {
                    print_r("ejecutando1 id:". $detail->id . "\n" );
                }
            }
            */
        }
    }

    function object_sorter($clave,$orden=null) {
        return function ($a, $b) use ($clave,$orden) {
              $result=  ($orden=="DESC") ? strnatcmp($b->$clave, $a->$clave) :  strnatcmp($a->$clave, $b->$clave);
              return $result;
        };
    }
}
