<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Admin\ExtractionHeaderModel;
use App\Models\Admin\ExtractionModel;
use App\Models\Admin\ExtractionReportModel;

class ExtractionDataProvider extends ServiceProvider
{
    public function run($params){
        $this->regularizar_campos_faltantes($params[1]);
    }

    public function regularizar_campos_faltantes($idHeader){
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("::::::::::::: regularizar_campos_faltantes  :::::::::::\n" );
        print_r(":::::::::::::::::::::::::::::::::::::::::::::::::::::::\n" );
        print_r("idHeader :". $idHeader . "\n" );
        $objHeader = ExtractionHeaderModel::find($idHeader);
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

$objExxtractionData = new ExtractionDataProvider();
$objExxtractionData->run($argv);
