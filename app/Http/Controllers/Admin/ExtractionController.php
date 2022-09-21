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
use App\Exports\ExtractionReportGeneralExport;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        set_time_limit(3600);
        ini_set('memory_limit', '2048M');
        ini_set('opcache.enable', '0');
        // $responsable = $request->input('responsable');
        $userId = Auth::id();
        $user = User::find($userId);
        if (is_object($user)) {
            $responsable = $user->name;
        }
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
                // Excel::queueImport(new ExtractionImport($idHeader,$responsable), request()->file('excelin')->store('temp'));
                ExtractionDataSearch::dispatch($extractionHeader);
                return redirect()->to(url('admin/extraccion/import/subidas'))->with('success',"Carga realizada con exito");
            }



        }else{
            return redirect()->to(url('extraccion/import'))->with('error',"Hubo problemas con el archivo");
        }

        $response = [
            'status' => 200,
            'data' => $responsable
        ];
        return json_encode($response);
    }

    /* ::: listamos todas las cargas que se realizaron :::: */
    public function getListChargeHeader(){
        $registros = ExtractionHeaderModel::selectRaw('*,FROM_UNIXTIME(datetimecreated) as fecha_creacion')->get()->sortByDesc('id');
        return view('admin.extraction.data_header', compact('registros'));
    }

    /* ::: obtenemos el detalle de la carga ::: */
    public function getListByHeaderId($idHeader){
        if ($idHeader > 0) {
            $objHeader = ExtractionHeaderModel::find($idHeader);
            //$lstDetalle = ExtractionModel::where('extractionHeaderId',$idHeader)->limit(1000)->get()->sortByDesc('id');
            $lstDetalle = NULL;
            return view('admin.extraction.data_detail', compact('objHeader','lstDetalle'));
        }else{
            return redirect()->to(url('extraccion/import'))->with('error',"Hubo problemas con el archivo");
        }
    }

    /* ::: exportamos la data :::: */
    public function getExcelDataByHeader($idHeader){
        $fecha = date('d-m-Y');
        return Excel::download(new ExtractionReportExport($idHeader), 'reporte_final_'.$fecha.'.xlsx');
    }

    public function getExcelDataGeneralByHeader($idHeader){
        $fecha = date('d-m-Y');
        return Excel::download(new ExtractionReportGeneralExport($idHeader), 'reporte_general_'.$fecha.'.xlsx');
    }

    public function getListDataUpload (Request $request){
        //var_dump($request->input('draw'));
        $idHeader = $request->get('idHeader');
        $draw = $request->get('draw');
        $start = $request->get('start');
        $rowperpage = $request->get('length');
        //dd($request->all());
        // Total records
        $totalRecords = ExtractionModel::selectRaw('count(*) as allcount')->where('extractionHeaderId',$idHeader)->count();
        //$totalRecordswithFilter = ExtractionModel::select('count(*) as allcount')->where('extractionHeaderId',$idHeader)->count();
        $select = '*';
        $lstDetalle = ExtractionModel::where('extractionHeaderId',$idHeader)->where('isActive',1)->where('isDeleted',0);
        $columnas = $request->input('columns');
        // recorremos por los where
        if (count($columnas)>0) {
            foreach ($columnas as $c => $col) {
                if ($col['searchable']) {
                    if (!is_null($col['search']['value'])) {
                        $select.=',IF(';
                        $select.='LOCATE("'.$col['search']['value'].'",'.$col['name'].') > 0,';
                        $select.= 'LOCATE("'.$col['search']['value'].'",'.$col['name'].'),"NOT FOUND") AS '.$col['name'].'_'.$c;
                        $lstDetalle->whereRaw($col['name']." like '%".$col['search']['value']."%'");
                    }
                }
            }
        }
        $orderBy = '';
        //recorremos por los order by
        /*
        if (count($columnas)>0) {
            foreach ($columnas as $c => $col) {
                if ($col['searchable']) {
                    if (!is_null($col['search']['value'])) {
                        $orderBy = "WHEN ".$col['name']." like '%".$col['search']['value']."' then ".($c+1);
                    }
                }
            }
        }

        if (strlen(trim($orderBy))>0) {
            $lstDetalle->orderByRaw('CASE '.$orderBy.' ELSE 20 END');
        }
        */
        $lstDetalle->selectRaw($select);
        if (count($columnas)>0) {
            foreach ($columnas as $c => $col) {
                if ($col['searchable']) {
                    if (!is_null($col['search']['value'])) {
                       // $orderBy = "WHEN ".$col['name']." like '%".$col['search']['value']."' then ".($c+1);
                       $lstDetalle->orderBy($col['name']."_".$c,'ASC');
                    }
                }
            }
        }

        // if (strlen(trim($orderBy))>0) {
        //     $lstDetalle->orderByRaw('CASE '.$orderBy.' ELSE 20 END');
        // }

        //$recordsWithFilter = $lstDetalle;
        $lstDetalle = $lstDetalle->skip($start)->take($rowperpage)->get();
        //$limit = $totalRecords - $start;
        // dd($limit);

        // dd($lstDetalle);
        // dd($recordsWithFilter->count());
        $lstDetalleCount = ExtractionModel::where('extractionHeaderId',$idHeader)->where('isActive',1)->where('isDeleted',0);
        $columnas = $request->input('columns');
        // recorremos por los where
        if (count($columnas)>0) {
            foreach ($columnas as $c => $col) {
                if ($col['searchable']) {
                    if (!is_null($col['search']['value'])) {
                        $select.=',IF(';
                        $select.='LOCATE("'.$col['search']['value'].'",'.$col['name'].') > 0,';
                        $select.= 'LOCATE("'.$col['search']['value'].'",'.$col['name'].'),"NOT FOUND") AS '.$col['name'].'_'.$c;
                        $lstDetalleCount->whereRaw($col['name']." like '%".$col['search']['value']."%'");
                    }
                }
            }
        }
        $totalRecordswithFilter = $lstDetalleCount->count();
        // dd($lstDetalle);
        /*
        if (!is_null($request->input('search'))) {
            $search = $request->input('search');
            $searchValue = $search['value'];
            $columnas = $request->input('columns');
            if (!is_null($searchValue) && count($columnas)>0) {
                foreach ($columnas as $c => $col) {
                    if ($col['searchable']) {
                        dd($request->request->all());
                        if (!is_null($col['search']['value'])) {
                            # code...
                        }
                    }
                }
            }
        }*/

        $data_arr = array();
        foreach($lstDetalle as $detalle){

           $data_arr[] = array(
               "id"                             => $detalle->id,
               "dua"                            => $detalle->dua,
               "fecha"                          => $detalle->fecha,
               "eta"                            => $detalle->eta,
               "codImporter"                    => $detalle->codImporter,
               "importador"                     => $detalle->importador,
               "codProvider"                    => $detalle->codProvider,
               "embarcadorExportador"           => $detalle->embarcadorExportador,
               "pesoBruto"                      => $detalle->pesoBruto,
               "pesoNeto"                       => $detalle->pesoNeto,
               "qty1"                           => $detalle->qty1,
               "und1"                           => $detalle->und1,
               "qty2"                           => $detalle->qty2,
               "und2"                           => $detalle->und2,
               "fobTotal"                       => $detalle->fobTotal,
               "fobUnd1"                        => $detalle->fobUnd1,
               "fobUnd2"                        => $detalle->fobUnd2,
               "codPaisOrigen"                  => $detalle->codPaisOrigen,
               "paisOrigen"                     => $detalle->paisOrigen,
               "codPaisCompra"                  => $detalle->codPaisCompra,
               "paisCompra"                     => $detalle->paisCompra,
               "puertoEmbarque"                 => $detalle->puertoEmbarque,
               "agenteAduanero"                 => $detalle->agenteAduanero,
               "estado"                         => $detalle->estado,
               "descripcionComercial"           => $detalle->descripcionComercial,
               "marca"                          => $detalle->marca,
               "nameMarca"                      => $detalle->nameMarca,
               "codigo"                         => $detalle->codigo,
               "status"                         => $detalle->status,
               'statusImporter'                 => $detalle->statusImporter,
               'statusProvider'                 => $detalle->statusProvider,
               'statusArticle'                  => $detalle->statusArticle
           );
        }

        $response = array(
            "draw" => intval($draw),
            "iTotalRecords" => $totalRecords,
            "iTotalDisplayRecords" => $totalRecordswithFilter,
            //"iTotalRecords" => count($lstDetalle),
            //"iTotalDisplayRecords" => count($lstDetalle),
            "aaData" => $data_arr
         );
        echo json_encode($response);
    }

    public function executeActionData(Request $request){
        $id = $request->get('id');
        $accion = $request->get('action');
        $userId = Auth::id();
        $user = User::find($userId);
        if (is_object($user)) {
            $responsable = $user->name;
        }else{
            $responsable = "";
        }
        if($accion == 'edit'){
            // $data = array(
            // 'dua'                   => $request->get('dua'),
            // 'fecha'                 => $request->get('fecha'),
            // 'eta'                   => $request->get('eta'),
            // 'importador'            => $request->get('importador'),
            // 'embarcadorExportador'  => $request->get('embarcadorExportador'),
            // 'pesoBruto'             => $request->get('pesoBruto'),
            // 'pesoNeto'              => $request->get('pesoNeto'),
            // 'qty1'                  => $request->get('qty1'),
            // 'und2'                  => $request->get('und2'),
            // 'qty2'                  => $request->get('qty2'),
            // 'und2'                  => $request->get('und2'),
            // 'fobTotal'              => $request->get('fobTotal'),
            // 'fobUnd1'               => $request->get('fobUnd1'),
            // 'fobUnd2'               => $request->get('fobUnd2'),
            // 'codPaisOrigen'         => $request->get('codPaisOrigen'),
            // 'paisOrigen'            => $request->get('paisOrigen'),
            // 'codPaisCompra'         => $request->get('codPaisCompra'),
            // 'paisCompra'            => $request->get('paisCompra'),
            // 'puertoEmbarque'        => $request->get('puertoEmbarque'),
            // 'agenteAduanero'        => $request->get('agenteAduanero'),
            // 'estado'                => $request->get('estado'),
            // 'descripcionComercial'  => $request->get('descripcionComercial'),
            // 'marca'                 => $request->get('marca'),
            // 'nameMarca'             => $request->get('nameMarca'),
            // 'codigo'                => $request->get('codigo'),
            // 'status'                => $request->get('status')
            // );

            $data = array(
                'dua'                   => $request->get('dua'),
                'fecha'                 => $request->get('fecha'),
                'codigo'                => $request->get('codigo'),
                'codImporter'           => $request->get('codImporter'),
                'importador'            => $request->get('importador'),
                'codProvider'           => $request->get('codProvider'),
                'embarcadorExportador'  => $request->get('embarcadorExportador'),
                'qty2'                  => $request->get('qty2'),
                'und2'                  => $request->get('und2'),
                'fobTotal'              => $request->get('fobTotal'),
                'fobUnd2'               => $request->get('fobUnd2'),
                'codPaisOrigen'         => $request->get('codPaisOrigen'),
                'paisOrigen'            => $request->get('paisOrigen'),
                'codPaisCompra'         => $request->get('codPaisCompra'),
                'paisCompra'            => $request->get('paisCompra'),
                'puertoEmbarque'        => $request->get('puertoEmbarque'),
                'descripcionComercial'  => $request->get('descripcionComercial'),
                'marca'                 => $request->get('marca'),
                'nameMarca'             => $request->get('nameMarca'),
                'datetimemodified'      => time(),
                'usrModified'           => $responsable
            );

            ExtractionModel::where('id', $id)
                    ->update($data);
            $status = 200;
            $message = "Se modificó con éxito";
            $rspta = array(
                'status' => $status,
                'message' => $message,
                'action' => $accion,
                'datetimemodified' => time(),
                'usrModified' => $responsable
            );
            return json_encode($rspta);
        }
        if($accion == 'delete'){
            $data = array(
                'isActive'  => 0,
                'isDeleted' => 1
            );
            ExtractionModel::where('id', $id)
                    ->update($data);
            $status = 200;
            $message = "Se eliminó con éxito";
            $rspta = array(
                'status' => $status,
                'message' => $message,
                'action' => $accion
            );
            return json_encode($rspta);
        }
    }

    public function repeatProcess(Request $request){
        $idHeader = $request->get('idHeader');
        $extractionHeader = ExtractionHeaderModel::find($idHeader);
        if (is_object($extractionHeader)) {
            ExtractionDataSearch::dispatch($extractionHeader);
            $status = 200;
            $msg = "La tarea se está ejecutando correctamente";
        }else{
            $status = 400;
            $msg = "No se encontró información sobre esta carga";
        }
        $result = [
            'status' => $status,
            'message' => $msg
        ];
        return json_encode($result);
    }

    public function test(){
        print_r("::::::::::::: test controller  :::::::::::\n" );
    }
}
