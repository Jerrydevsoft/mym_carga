<?php

namespace App\Crons;
$vendorDir = dirname(dirname(__FILE__));
$baseDir = dirname($vendorDir);
global $argc, $argv;
use App\Http\Controllers\Admin\ExtractionController;
use app\Models\Admin\ExtractionHeaderModel;
use app\Models\Admin\ExtractionModel;
use app\Models\Admin\ExtractionReportModel;
//use App\Models\Admin\ExtractionHeaderModel;
// use App\Imports\ExtractionImport;
// use App\Exports\ExtractionReportExport;
use Illuminate\Http\Request;

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
        var_dump(\App\Http\Controllers\Admin\ExtractionController::test());
        //var_dump($model);
        //$objHeader = ExtractionHeaderModel::find($idHeader);
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
