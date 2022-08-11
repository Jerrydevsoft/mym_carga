<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin\ExtractionHeaderModel;
use App\Models\Admin\ExtractionModel;
use App\Models\Admin\ExtractionReportModel;
use Illuminate\Support\Facades\Validator;
use App\Jobs\ExtractionDataSearch;
use App\Jobs\UpdateCountries;
use App\Imports\ExtractionImport;
use App\Exports\ExtractionReportExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ExtractionController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
    }

    public function import(Request $request)
    {
        //Excel::import(new ExtractionImport, request()->file('your_file'));

        //return redirect('/')->with('success', 'All good!');
        UpdateCountries::dispatch();
        return view('admin.extraction.import');
    }

    public function importData(Request $request){
        $responsable = $request->input('responsable');
        $idHeader = 0;

        if ($request->file('excelin')) {
            $extractionHeader = new ExtractionHeaderModel;
            $extractionHeader->description = $request->file('excelin')->getClientOriginalName();
            $extractionHeader->datetimecreated = time();
            $extractionHeader->datetimemodified = null;
            $extractionHeader->usrCreated = $responsable;
            $extractionHeader->totalRegister = null;
            $extractionHeader->totalFound = null;
            $extractionHeader->totalMissing = null;
            $extractionHeader->status="STARTED";
            $extractionHeader->save();
            if ($extractionHeader->id > 0) {
                // $extractionHeader->id = 3;
                $idHeader = $extractionHeader->id;
                $response = Excel::import(new ExtractionImport($idHeader,$responsable), request()->file('excelin')->store('temp'));
                ExtractionDataSearch::dispatch($extractionHeader);
                return redirect()->to(url('admin/extraccion/import/subidas'))->with('success',"Carga realizada con exito");
            }



        }else{
            return redirect()->to(url('extraccion/import'))->with('error',"Hubo problemas con el archivo");
        }
    }

    /* ::: listamos todas las cargas que se realizaron :::: */
    public function getListChargeHeader(){
        $registros = ExtractionHeaderModel::get()->sortByDesc('id');

        return view('admin.extraction.data_header', compact('registros'));
    }

    /* ::: obtenemos el detalle de la carga ::: */
    public function getListByHeaderId($idHeader){
        if ($idHeader > 0) {
            $objHeader = ExtractionHeaderModel::find($idHeader);
            $lstDetalle = ExtractionModel::where('extractionHeaderId',$idHeader)->get()->sortByDesc('id');
            return view('admin.extraction.data_detail', compact('objHeader','lstDetalle'));
        }else{
            return redirect()->to(url('extraccion/import'))->with('error',"Hubo problemas con el archivo");
        }
    }

    /* ::: exportamos la data :::: */
    public function getExcelDataByHeader($idHeader){
        return Excel::download(new ExtractionReportExport($idHeader), 'reporte_extracction.xlsx');
    }

    public function getTableByHeaderId(Request $request){
        dd($request);
    }

    public function test(){
        print_r("::::::::::::: test controller  :::::::::::\n" );
    }
}
