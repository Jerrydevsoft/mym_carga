@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>ARCHIVO: {{ $objHeader->description }}</h1>
@stop

@section('content')
        <div class="card">
            <div class="card-header">
                Filtro de Busqueda
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <input class="form-control" type="text" id="searchDescription" name="searchDescription" value="" placeholder="Buscar por descripcion">
                    </div>
                    <div class="col-md-4">
                        &nbsp;
                    </div>
                    <div class="col-md-4">
                        <select id="searchStatus" name="searchStatus" class="form-control">
                            <option value="CARGA_INICIAL">CARGA_INICIAL</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="card">
            <div class="card-header">
                <h3>Listado</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12" style="text-align: right;">
                        <input type="hidden" value="{{ $objHeader->id }}" id="idHeader">
                        <a href="{{ route('extraccion.exportData.excel', ['idHeader' => $objHeader->id])  }}" class="btn btn-success">Exportar</a>
                    </div>
                    <br>
                    <div class="col-md-12" style="height: 800px; overflow-x: auto;">
                        <table class="table table-hover" id="tableDetail">
                            <thead>
                                <tr>
                                    <th>PARTIDA ADUANERA</th>
                                    <th>ADUANA</th>
                                    <th>DUA</th>
                                    <th>FECHA</th>
                                    <th>ETA</th>
                                    <th>NUMERO DE MANIFIESTO</th>
                                    <th>CÓDIGO TRIBUTARIO</th>
                                    <th>IMPORTADOR</th>
                                    <th>EMBARCADOR / EXPORTADOR</th>
                                    <th>PESO BRUTO</th>
                                    <th>PESO NETO</th>
                                    <th>QTY1</th>
                                    <th>UNID1</th>
                                    <th>QTY2</th>
                                    <th>UNID2</th>
                                    <th>FOB TOTAL</th>
                                    <th>FOB UND 1</th>
                                    <th>FOB UND 2</th>
                                    <th>PAIS ORIGEN</th>
                                    <th>PAIS COMPRA</th>
                                    <th>PUERTO EMBARQUE</th>
                                    <th>AGENTE ADUANERO</th>
                                    <th>ESTADO</th>
                                    <th>DESCRIPCION COMERCIAL</th>
                                    <th>DESCRIPCION 1</th>
                                    <th>DESCRIPCION 2</th>
                                    <th>DESCRIPCION 3</th>
                                    <th>DESCRIPCION 4</th>
                                    <th>DESCRIPCION 5</th>
                                    <th>MARCA</th>
                                    <th>CÓDIGO</th>
                                    <th>ESTADO CARGA</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if (!is_null($lstDetalle))
                                @foreach($lstDetalle as $detalle)
                                    <tr>
                                        <td>{{ $detalle->partidaAduanera }}</td>
                                        <td>{{ $detalle->aduana }}</td>
                                        <td>{{ $detalle->dua }}</td>
                                        <td>{{ $detalle->fecha }}</td>
                                        <td>{{ $detalle->eta }}</td>
                                        <td>{{ $detalle->numManifiesto }}</td>
                                        <td>{{ $detalle->codTributario }}</td>
                                        <td>{{ $detalle->importador }}</td>
                                        <td>{{ $detalle->embarcadorExportador }}</td>
                                        <td>{{ $detalle->pesoBruto }}</td>
                                        <td>{{ $detalle->pesoNeto }}</td>
                                        <td>{{ $detalle->qty1 }}</td>
                                        <td>{{ $detalle->und1 }}</td>
                                        <td>{{ $detalle->qty2 }}</td>
                                        <td>{{ $detalle->und2 }}</td>
                                        <td>{{ $detalle->fobTotal }}</td>
                                        <td>{{ $detalle->fobUnd1 }}</td>
                                        <td>{{ $detalle->fobUnd2 }}</td>
                                        <td>{{ $detalle->paisOrigen }}</td>
                                        <td>{{ $detalle->paisCompra }}</td>
                                        <td>{{ $detalle->puertoEmbarque }}</td>
                                        <td>{{ $detalle->agenteAduanero }}</td>
                                        <td>{{ $detalle->estado }}</td>
                                        <td>{{ $detalle->descripcionComercial }}</td>
                                        <td>{{ $detalle->descripcion1 }}</td>
                                        <td>{{ $detalle->descripcion2 }}</td>
                                        <td>{{ $detalle->descripcion3 }}</td>
                                        <td>{{ $detalle->descripcion4 }}</td>
                                        <td>{{ $detalle->descripcion5 }}</td>
                                        <td>{{ $detalle->marca }}</td>
                                        <td>{{ $detalle->codigo }}</td>
                                        <td>
                                            <span class="{{$detalle->typeFoundColor}}">{{$detalle->status}}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
        $(document).ready(function(){
            extraction_cargarDatatablePost('tableDetail');
        });

        function extraction_cargarDatatablePost(idTable){
            $("#tableDetail").DataTable();
            /*
            $("#"+idTable).DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('extraccion.data.table') }}",
                    type: 'POST',
                    'data' : { 'idHeader' : $("#idHeader").val() },
                },
                'order': [],
                columns: [
                    { data: 'dua' },
                    { data: 'fecha' },
                    { data: 'codigo' },
                    { data: 'importador' },
                    { data: 'embarcadorExportador' },
                    { data: 'qty2' },
                    { data: 'und2' },
                    { data: 'fobTotal' },
                    { data: 'fobUnd1' },
                    { data: 'paisOrigen' },
                    { data: 'paisCompra' },
                    { data: 'puertoEmbarque' },
                    { data: 'marca' },
                ]
            });
            */
           
        }
    </script>
@stop
