<?php

namespace App\Imports;

use App\Models\Admin\ExtractionModel;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ExtractionImport implements ToModel, WithStartRow
{

    public function __construct(int $idHeader,string $responsable)
    {
        $this->idHeader = $idHeader;
        $this->responsable = $responsable;
    }
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function startRow(): int
    {
        return 2;
    }


    public function model(array $row)
    {
        return new ExtractionModel([
            'partidaAduanera'           => $row['0'],
            'aduana'                    => $row['1'],
            'dua'                       => $row['2'],
            //'fecha'                     => (trim($row['3']!=''))?date('Y-m-d',strtotime($row['3'])):null,
            //'eta'                       => (trim($row['4']!=''))?date('Y-m-d',strtotime($row['4'])):null,
            'fecha'                     => (trim($row['3']!=''))?$this->transformDate($row[3]):null,
            'eta'                       => (trim($row['4']!=''))?$this->transformDate($row[4]):null,
            'numManifiesto'             => $row['5'],
            'codTributario'             => $row['6'],
            'importador'                => $row['7'],
            'embarcadorExportador'      => $row['8'],
            'pesoBruto'                 => $row['9'],
            'pesoNeto'                  => $row['10'],
            'qty1'                      => $row['11'],
            'und1'                      => $row['12'],
            'qty2'                      => $row['13'],
            'und2'                      => $row['14'],
            'fobTotal'                  => $row['15'],
            'fobUnd1'                   => $row['16'],
            'fobUnd2'                   => $row['17'],
            'paisOrigen'                => $row['18'],
            'paisCompra'                => $row['19'],
            'puertoEmbarque'            => $row['20'],
            'agenteAduanero'            => $row['21'],
            'estado'                    => $row['22'],
            'descripcionComercial'      => $row['23'],
            'descripcion1'              => $row['24'],
            'descripcion2'              => $row['25'],
            'descripcion3'              => $row['26'],
            'descripcion4'              => $row['27'],
            'descripcion5'              => $row['28'],
            'marca'                     => '',
            'codigo'                    => '',
            'opcional1'                 => (!isset($row['31']))?'':$row['31'],
            'opcional2'                 => (!isset($row['32']))?'':$row['32'],
            'opcional3'                 => (!isset($row['33']))?'':$row['33'],
            'opcional4'                 => (!isset($row['34']))?'':$row['34'],
            'opcional5'                 => (!isset($row['35']))?'':$row['35'],
            'usrCreated'                => $this->responsable,
            'usrModified'               => NULL,
            'status'                    => 'CHARGED',
            'statusArticle'             => 'CHARGED',
            'statusProvider'            => 'CHARGED',
            'statusImporter'            => 'CHARGED',
            'datetimecreated'           => time(),
            'datetimemodified'          => NULL,
            'extractionHeaderId'        => $this->idHeader,
            'typeFoundColor'            => 'badge bg-primary',
            'typeFoundArticle'          => 'badge bg-primary'
        ]);
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return \Carbon\Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\ErrorException $e) {
            return \Carbon\Carbon::createFromFormat($format, $value);
        }
    }

    // public function chunkSize(): int
    // {
    //     return 10000;
    // }
}
