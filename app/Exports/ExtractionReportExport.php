<?php

namespace App\Exports;

use App\Models\Admin\ExtractionReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Illuminate\Support\Facades\DB;

class ExtractionReportExport implements FromCollection,WithHeadings,WithMapping
{
    //WithColumnFormatting,WithMapping,
    public function __construct(int $idHeader)
    {
        $this->idHeader = $idHeader;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        //return ExtractionReportModel::all();
        //return ExtractionReportModel::where('extractionHeaderId',$this->idHeader)->get()->withHeadings();
        // return ExtractionReportModel::select(['dua',DB::raw( date_format( `fecha`, '%d/%m/%Y' ) as ),'codigo','codImporter','codProvider','qty2','und2','fobTotal','fobUnd1','codPaisOrigen','codPaisCompra','puertoEmbarque','marca'])
        //                             ->where('extractionHeaderId',$this->idHeader)
        //                             ->get();
        return ExtractionReportModel::selectRaw("dua,DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha,codigo,codImporter,codProvider,qty2,und2,fobTotal,fobUnd1,codPaisOrigen,codPaisCompra,puertoEmbarque,marca")
                                    ->where('extractionHeaderId',$this->idHeader)
                                    ->get();
        // return ExtractionReportModel::selectRaw("dua,DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha,codigo,codImporter,codProvider,qty2,und2,fobTotal,fobUnd1,codPaisOrigen,codPaisCompra,puertoEmbarque,marca")
        //                             ->where('extractionHeaderId',$this->idHeader)
        //                             ->get()->flatMap(function($data){
        //                                 return [
        //                                     'DUA' => $data->dua,
        //                                     'FECHA_DUA' => $data->fecha,
        //                                     'CODFABRICACION' => $data->codigo,
        //                                     'IMPORTADOR' => $data->codImporter,
        //                                     'PROVEEDOR' => $data->codProvider,
        //                                     'QTY2' => $data->qty2,
        //                                     'UND2' => $data->und2,
        //                                     'U$FOBTOT' => $data->fobTotal,
        //                                     'U$FOBUND' => $data->fobUnd1,
        //                                     'PAISORIGEN' => $this->completar_ceros($data->codPaisOrigen),
        //                                     'PAISCOMPRA' => $this->completar_ceros($data->codPaisCompra),
        //                                     'EMBARQUE' => $data->puertoEmbarque,
        //                                     'MARCA' => $data->marca
        //                                  ];
        //                             });
        /*
        return ExtractionReportModel::select('dua','fecha','codigo','importador','embarcadorExportador','qty2','und2','fobTotal','fobUnd1','paisOrigen','paisCompra','puertoEmbarque','marca')
                                    ->where('extractionHeaderId',$this->idHeader)
                                    ->get()->flatMap(function($data) {
            return [
               'DUA' => $data->dua,
               'FECHA_DUA' => $data->fecha,
               'CODFABRICACION' => $data->codigo,
               'IMPORTADOR' => $data->importador,
               'PROVEEDOR' => $data->embarcadorExportador,
               'QTY2' => $data->qty2,
               'UND2' => $data->und2,
               'U$FOBTOT' => $data->fobTotal,
               'U$FOBUND' => $data->fobUnd1,
               'PAISORIGEN' => $data->paisOrigen,
               'PAISCOMPRA' => $data->paisCompra,
               'EMBARQUE' => $data->puertoEmbarque,
               'MARCA' => $data->marca
            ];
         });
        */
    }

    public function headings(): array
    {
        return ['DUA','FECHA DUA', 'CODFABRICACION','IMPORTADOR','PROVEEDOR','QTY2','UND2','U$FOBTOT','U$FOBUND','PAISORIGEN','PAISCOMPRA','EMBARQUE','MARCA'];
        //return ["id", "partidaAduanera", "aduana", "dua", "fecha", "eta", "numManifiesto", "codTributario", "importador", "embarcadorExportador", "pesoBruto", "pesoNeto", "qty1", "und1", "qty2", "und2", "fobTotal", "fobUnd1", "fobUnd2", "paisOrigen", "paisCompra", "puertoEmbarque", "agenteAduanero", "estado", "descripcionComercial", "descripcion1", "descripcion2", "descripcion3", "descripcion4", "descripcion5", "marca", "codigo", "opcional1", "opcional2", "opcional3", "opcional4", "opcional5", "usrCreated", "usrModified", "status", "datetimecreated", "datetimemodified", "extractionHeaderId"];
    }

    public function map($invoice): array
    {
        return [
            $invoice->dua,
            $invoice->fecha,
            $invoice->codigo,
            $invoice->codImporter,
            $invoice->codProvider,
            $invoice->qty2,
            $invoice->und2,
            $invoice->fobTotal,
            $invoice->fobUnd1,
            $this->completar_ceros($invoice->codPaisOrigen),
            $this->completar_ceros($invoice->codPaisCompra),
            $invoice->puertoEmbarque,
            $invoice->marca
        ];
    }

    public function completar_ceros($number){
        $result = null;
        if (strlen(trim($number))>0) {
            switch (strlen(trim($number))) {
                case 1:
                    $result = '00'.trim($number);
                    break;
                case 2:
                    $result = '0'.trim($number);
                    break;
                default:
                    $result = trim($number);
                    break;
            }
        }
        return $result;
    }

    // public function columnFormats(): array
    // {
    //     return [
    //         'B' => NumberFormat::FORMAT_DATE_DDMMYYYY,
    //         'C' => NumberFormat::FORMAT_CURRENCY_EUR_SIMPLE,
    //     ];
    // }

}
