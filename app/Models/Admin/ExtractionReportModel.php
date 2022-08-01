<?php

namespace App\Models\Admin;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExtractionReportModel extends Model
{
    use HasFactory;
    protected $table = 'extraction_subida';
    public $timestamps = false;
    protected $fillable = ['partidaAduanera', 'aduana', 'dua', 'fecha', 'eta', 'numManifiesto', 'codTributario', 'importador','embarcadorExportador', 'pesoBruto', 'pesoNeto', 'qty1', 'und1', 'qty2','und2', 'fobTotal', 'fobUnd1', 'fobUnd2', 'paisOrigen', 'paisCompra', 'puertoEmbarque', 'agenteAduanero', 'estado', 'descripcionComercial','descripcion1', 'descripcion2','descripcion3', 'descripcion4', 'descripcion5','marca', 'codigo', 'largo', 'opcional1', 'opcional2', 'opcional3', 'opcional4', 'opcional5', 'usrCreated', 'usrModified', 'status', 'datetimecreated', 'datetimemodified','extractionHeaderId'];
}
