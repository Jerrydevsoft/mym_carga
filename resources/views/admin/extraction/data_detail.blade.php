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
                    <div class="col-md-12" style="height: 800px;">
                        <table class="table table-hover nowrap" id="tableDetail" style="width:100%">
                            <thead>
                                <tr>
                                    <th>DUA</th>
                                    <th>FECHA</th>
                                    <th>ETA</th>
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
                                    <th>COD PAIS ORIGEN</th>
                                    <th>PAIS ORIGEN</th>
                                    <th>COD PAIS COMPRA</th>
                                    <th>PAIS COMPRA</th>
                                    <th>PUERTO EMBARQUE</th>
                                    <th>AGENTE ADUANERO</th>
                                    <th>ESTADO</th>
                                    <th>DESCRIPCION COMERCIAL</th>
                                    <th>COD MARCA</th>
                                    <th>NOMBRE MARCA</th>
                                    <th>CÓDIGO</th>
                                    <th>ESTADO CARGA</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if (!is_null($lstDetalle))
                                @foreach($lstDetalle as $detalle)
                                    <tr>
                                        <td>{{ $detalle->dua }}</td>
                                        <td>{{ $detalle->fecha }}</td>
                                        <td>{{ $detalle->eta }}</td>
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
                                        <td>{{ $detalle->codPaisOrigen }}</td>
                                        <td>{{ $detalle->paisOrigen }}</td>
                                        <td>{{ $detalle->codPaisCompra }}</td>
                                        <td>{{ $detalle->paisCompra }}</td>
                                        <td>{{ $detalle->puertoEmbarque }}</td>
                                        <td>{{ $detalle->agenteAduanero }}</td>
                                        <td>{{ $detalle->estado }}</td>
                                        <td>{{ $detalle->descripcionComercial }}</td>
                                        <td>{{ $detalle->marca }}</td>
                                        <td>{{ $detalle->nameMarca }}</td>
                                        <td>{{ $detalle->codigo }}</td>
                                        <td>
                                            <span class="{{$detalle->typeFoundColor}}">{{$detalle->status}}</span>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>DUA</th>
                                    <th>FECHA</th>
                                    <th>ETA</th>
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
                                    <th>COD PAIS ORIGEN</th>
                                    <th>PAIS ORIGEN</th>
                                    <th>COD PAIS COMPRA</th>
                                    <th>PAIS COMPRA</th>
                                    <th>PUERTO EMBARQUE</th>
                                    <th>AGENTE ADUANERO</th>
                                    <th>ESTADO</th>
                                    <th>DESCRIPCION COMERCIAL</th>
                                    <th>COD MARCA</th>
                                    <th>NOMBRE MARCA</th>
                                    <th>CÓDIGO</th>
                                    <th>ESTADO CARGA</th>
                                </tr>
                            </tfoot>
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
            $('#tableDetail tfoot th').each( function () {
                var title = $(this).text();
                $(this).html('<input type="text" placeholder="Search ' + title + '" />');
            } );
            var table = $('#tableDetail').DataTable({
                            "pageLength": 100,
                            "lengthMenu": [ 100, 250, 500],
                            fixedHeader: {
                                header: true,
                                footer: true
                            },
                            scrollY: 500,
                            scrollX: true,
                            initComplete: function () {
                                // Apply the search
                                this.api()
                                    .columns()
                                    .every(function () {
                                        var that = this;

                                        $('input', this.footer()).on('keyup change clear', function () {
                                            if (that.search() !== this.value) {
                                                that.search(this.value).draw();
                                            }
                                        });
                                    });
                            },
                        });

            table.columns().eq( 0 ).each( function ( colIdx ) {
                $( 'input', table.column( colIdx ).header() ).on( 'keyup change', function () {
                    table
                        .column( colIdx )
                        .search( this.value )
                        .draw();
                    } );
                } );
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
