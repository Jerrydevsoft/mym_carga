<?php

namespace App\Exports;

use App\Models\Admin\ExtractionReportModel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExtractionReportGeneralExport implements FromCollection,WithHeadings
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
        return ExtractionReportModel::select("id", "partidaAduanera", "aduana", "dua", "fecha", "eta", "numManifiesto", "codTributario", "codImporter","importador", "codProvider","embarcadorExportador", "pesoBruto", "pesoNeto", "qty1", "und1", "qty2", "und2", "fobTotal", "fobUnd1", "fobUnd2", "codPaisOrigen","paisOrigen","codPaisCompra","paisCompra", "puertoEmbarque", "agenteAduanero", "estado", "descripcionComercial", "descripcion1", "descripcion2", "descripcion3", "descripcion4", "descripcion5", "marca", "codigo", "opcional1", "opcional2", "opcional3", "opcional4", "opcional5", "status")
        ->where('extractionHeaderId',$this->idHeader)
        ->get();
    }

    public function headings(): array
    {
        //return ['DUA','FECHA DUA', 'CODFABRICACION','IMPORTADOR','PROVEEDOR','QTY2','UND2','U$FOBTOT','U$FOBUND','PAISORIGEN','PAISCOMPRA','EMBARQUE','MARCA'];
        return ["ID", "PARTIDA ADUANERA", "ADUANA", "DUA", "FECHA", "ETA", "NUM MANIFIESTO", "COD TRIBUTARIO", "COD IMPORTADOR","IMPORTADOR", "COD PROVEEDOR","PROVEEDOR", "PESO BRUTO", "PESO NETO", "QTY1", "UND1", "QTY2", "UND2", 'U$FOBTOT','U$FOBUND1', 'U$FOBUND2', "COD PAIS ORIGEN","PAIS ORIGEN", "COD PAIS COMPRA","PAIS COMPRA", "PUERTO EMBARQUE", "AGENTE ADUANERO", "ESTADO", "DESCRIPCION COMERCIAL", "DESCRIPCION1", "DESCRIPCION2", "DESCRIPCION3", "DESCRIPCION4", "DESCRIPCION5", "MARCA", "CODIGO", "OPCIONAL1", "OPCIONAL2", "OPCIONAL3", "OPCIONAL4", "OPCIONAL5", "ESTADO SISTEMA"];
    }
}
