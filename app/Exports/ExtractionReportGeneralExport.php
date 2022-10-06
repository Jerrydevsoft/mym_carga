<?php

namespace App\Exports;

use App\Models\Admin\ExtractionReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Facades\DB;

class ExtractionReportGeneralExport implements FromCollection,WithMapping,WithHeadings
{
    public function __construct(int $idHeader)
    {
        $this->idHeader = $idHeader;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        // return ExtractionReportModel::select("id", "partidaAduanera", "aduana", "dua", "fecha", "eta", "numManifiesto", "codTributario", "codImporter","importador", "codProvider","embarcadorExportador", "pesoBruto", "pesoNeto", "qty1", "und1", "qty2", "und2", "fobTotal", "fobUnd1", "fobUnd2", "codPaisOrigen","paisOrigen","codPaisCompra","paisCompra", "puertoEmbarque", "agenteAduanero", "estado", "descripcionComercial", "descripcion1", "descripcion2", "descripcion3", "descripcion4", "descripcion5", "nameMarca", "codigo", "opcional1", "opcional2", "opcional3", "opcional4", "opcional5")
        // ->where('extractionHeaderId',$this->idHeader)
        // ->get();
        return ExtractionReportModel::selectRaw("id,partidaAduanera,aduana,dua,DATE_FORMAT(fecha,'%d/%m/%Y') AS fecha,DATE_FORMAT(eta,'%d/%m/%Y') AS eta,numManifiesto,codTributario,codImporter,importador,codProvider,embarcadorExportador,pesoBruto,pesoNeto,qty1,und1,qty2,und2,fobTotal,fobUnd1,fobUnd2,codPaisOrigen,paisOrigen,codPaisCompra,paisCompra,puertoEmbarque,agenteAduanero,estado,descripcionComercial,descripcion1,descripcion2,descripcion3,descripcion4,descripcion5,nameMarca,codigo,status,statusImporter,statusProvider,statusArticle")
        ->where('extractionHeaderId',$this->idHeader)
        ->where ('isActive',1)
        ->where('isDeleted',0)
        ->get();
    }

    public function headings(): array
    {
        return ['DUA','FECHA DUA', 'CODFABRICACION','IMPORTADOR','PROVEEDOR','QTY2','UND2','U$FOBTOT','U$FOBUND','PAISORIGEN','PAISCOMPRA','EMBARQUE','MARCA',"PARTIDA ADUANERA", "ADUANA", "DUA", "FECHA", "ETA", "NUM MANIFIESTO", "COD TRIBUTARIO","IMPORTADOR","ESTADO IMPORTADOR","PROVEEDOR", "ESTADO PROVEEDOR","PESO BRUTO", "PESO NETO", "QTY1", "UND1", "QTY2", "UND2", 'U$FOBTOT','U$FOBUND1', 'U$FOBUND2',"PAIS ORIGEN","PAIS COMPRA", "PUERTO EMBARQUE", "AGENTE ADUANERO", "DESCRIPCION COMERCIAL","NOMBRE MARCA","ESTADO MARCA","CODIGO","ESTADO ARTICULO"];
        //return ['DUA','FECHA DUA', 'CODFABRICACION','IMPORTADOR','PROVEEDOR','QTY2','UND2','U$FOBTOT','U$FOBUND','PAISORIGEN','PAISCOMPRA','EMBARQUE','MARCA'];
        // return ["ID", "PARTIDA ADUANERA", "ADUANA", "DUA", "FECHA", "ETA", "NUM MANIFIESTO", "COD TRIBUTARIO", "COD IMPORTADOR","IMPORTADOR", "COD PROVEEDOR","PROVEEDOR", "PESO BRUTO", "PESO NETO", "QTY1", "UND1", "QTY2", "UND2", 'U$FOBTOT','U$FOBUND1', 'U$FOBUND2', "COD PAIS ORIGEN","PAIS ORIGEN", "COD PAIS COMPRA","PAIS COMPRA", "PUERTO EMBARQUE", "AGENTE ADUANERO", "ESTADO", "DESCRIPCION COMERCIAL", "DESCRIPCION1", "DESCRIPCION2", "DESCRIPCION3", "DESCRIPCION4", "DESCRIPCION5", "MARCA", "CODIGO"];
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
            $invoice->marca,
            $invoice->partidaAduanera,
            $invoice->aduana,
            $invoice->dua,
            $invoice->fecha,
            $invoice->eta,
            $invoice->numManifiesto,
            $invoice->codTributario,
            $invoice->importador,
            $invoice->statusImporter,
            $invoice->embarcadorExportador,
            $invoice->statusProvider,
            $invoice->pesoBruto,
            $invoice->pesoNeto,
            $invoice->qty1,
            $invoice->und1,
            $invoice->qty2,
            $invoice->und2,
            $invoice->fobTotal,
            $invoice->fobUnd1,
            $invoice->fobUnd2,
            $invoice->paisOrigen,
            $invoice->paisCompra,
            $invoice->puertoEmbarque,
            $invoice->agenteAduanero,
            $invoice->descripcionComercial,
            $invoice->nameMarca,
            $invoice->status,
            $invoice->codigo,
            $invoice->statusArticle
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
}
