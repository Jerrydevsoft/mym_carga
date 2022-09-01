<?php

namespace App\Crons;
require_once "../../vendor/autoload.php";
//use App\Models\Admin\ExtractionHeaderModel;
// use App\Imports\ExtractionImport;
// use App\Exports\ExtractionReportExport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class ExtractionDataCron
{
    public function run($params){
        $this->regularizar_campos_faltantes($params[1]);
    }

    public function regularizar_campos_faltantes($idHeader){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("::::::::::::: regularizar_campos_faltantes  :::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("idHeader :". $idHeader . "\n" );
        //var_dump($model);
        $totalRegistros = DB::table("extraction_subida")->where('extractionHeaderId', $idHeader)->count();
        var_dump($totalRegistros);

        /*
        if (is_object($objHeader)) {
            //obtenemos el detalle de los registros subidos
            $lstDetail = App\Models\Admin\ExtractionModel::where('extractionHeaderId',$idHeader)
                                        ->where('status','CARGADO')
                                        ->get();
            if (is_array($lstDetail) && !empty($lstDetail)) {
                foreach ($lstDetail as $d => $detail) {
                    print_r("ejecutando id:". $detail->id . "\n" );
                }
            }
        }
        */
    }


}

$objExxtractionData = new ExtractionDataCron();
$objExxtractionData->run($argv);
