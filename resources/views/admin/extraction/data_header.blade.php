@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    {{-- <h1>Extracción</h1> --}}
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
                    <table class="table table-hover" id="tableHeader">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>DESCRIPCION</th>
                                <th>TOTAL DE REGISTRO</th>
                                <th>TOTAL ENCONTRADOS</th>
                                <th>TOTAL NO ENCONTRADOS</th>
                                <th>ESTADO</th>
                                <th>FECHA DE REGISTRO</th>
                                <th>RESPONSABLE</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                        @if (!is_null($registros))
                            @foreach($registros as $registro)
                                <tr>
                                    <td>{{ $registro->id }}</td>
                                    <td>{{ $registro->description }}</td>
                                    <td>{{ $registro->totalRegister }}</td>
                                    <td>{{ $registro->totalFound }}</td>
                                    <td>{{ $registro->totalMissing }}</td>
                                    <td>{{ $registro->status }}</td>
                                    <td>{{ $registro->fecha_creacion }}</td>
                                    <td>{{ $registro->usrCreated }}</td>
                                    <td>
                                        <a href="{{ route('extraccion.data.revision', ['idHeader' => $registro->id])  }}">revisar</a>&nbsp;&nbsp;
                                        <a href="javascript:void(0);" onclick="repeatProcess({{$registro->id}})">Procesar</a>&nbsp;&nbsp;
                                        <a href="javascript:void(0);" onclick="extraction_showResultExtraction({{$registro->id}})">Reporte Busqueda</a>
                                    </td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-result" style="display: none;" aria-hidden="true">
            <div class="modal-dialog-lg" style="width:800px; margin: auto;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">RESULTADO DE BUSQUEDA HASTA EL MOMENTO</h4>
                        <button type="button" class="close" onclick="extraction_closeModal('modal-result');" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div id="accordion">
                          <div class="card">
                            <div class="card-header" id="headingOne">
                              <h5 class="mb-0">
                                <button class="btn btn-link" onclick="extraction_toogleHeader('collapseOne');">
                                  Header
                                </button>
                              </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                              <div class="card-body" id="divHeader">
                              </div>
                            </div>
                          </div>
                          <div class="card">
                            <div class="card-header" id="headingTwo">
                              <h5 class="mb-0">
                                <button class="btn btn-link collapsed" onclick="extraction_toogleHeader('collapseTwo');">
                                  Detail
                                </button>
                              </h5>
                            </div>
                            <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion">
                              <div class="card-body" id="divDetail">
                              </div>
                            </div>
                          </div>
                        </div>
                    </div>
                </div>
            </div> 
        </div>
    </div>
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
    <style>

        .dataTables_paginate a {
            padding: 6px 9px !important;
            background: #edf6f9 !important;
            border-color: #b8bdc1 !important;
            border-radius: 5px;
            cursor: pointer;

        }

    </style>
@stop

@section('js')
    <script>
    $(document).ready(function(){
        $("#tableHeader").DataTable();
    });

    function repeatProcess(idHeader){
        if (idHeader > 0) {
            $.ajax(
                {
                    url: "{{ route('extraccion.data.reprocesar') }}",
                    type: 'POST',
                    data:{
                        idHeader: idHeader,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(result){
                        console.log(result);
                        alert("La información se esta volviendo a procesar");
                }});
        }
    }

    function extraction_toogleHeader(id){
        $("#"+ id).fadeToggle( "slow", "linear" );
    }

    function extraction_closeModal(id){
        $("#"+ id).modal('hide');
    }

    function extraction_showResultExtraction(idHeader){
        if (idHeader > 0) {
            var htmlHeader = '';
            var htmlDetail = '';
            $.ajax(
                {
                    url: "{{ route('extraccion.data.mostrar.result') }}",
                    type: 'POST',
                    dataType:'json',
                    data:{
                        idHeader: idHeader,
                        "_token": "{{ csrf_token() }}"
                    },
                    success: function(result){
                        // console.log(result.status);
                        if (result.status == 200) {
                            // console.log(result.status);
                            htmlHeader += '<div class="row">';
                            htmlHeader += '<div class="col-md-4">CAMPO</div>';
                            htmlHeader += '<div class="col-md-4">ESTADO</div>';
                            htmlHeader += '<div class="col-md-4">CANTIDAD</div>';
                            htmlHeader += '</div>';
                            $.each(result.dataHeader, function(i, item) {
                                htmlHeader += '<div class="row">';
                                htmlHeader += '<div class="col-md-4">'+item.campo+'</div>';
                                switch (item.estado) {
                                            case 'FOUNDED':
                                                //$(td +' .tabledit-span').addClass('badge bg-success');
                                                //$(td).css('margin', 'auto');
                                                //$(td).html("<span class='badge bg-success' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                htmlHeader += '<div class="col-md-4"><span class="badge bg-success" style="background-color: #28a745 !important;color:#ffffff;">'+item.estado+'</span></div>';
                                                break;
                                            case 'NOT_FOUND':
                                                htmlHeader += '<div class="col-md-4"><span class="badge bg-danger" style="font-color: #ec0404 !important;color:#ffffff;">'+item.estado+'</span></div>';
                                                //$(td +' .tabledit-span').addClass('badge bg-danger');
                                                //$(td).html("<span class='badge bg-danger' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'red');
                                                break;
                                            case 'PARTIAL_FOUND':
                                                htmlHeader += '<div class="col-md-4"><span class="badge bg-warning" style="background-color: #e7af3f !important;color:#ffffff;">'+item.estado+'</span></div>';
                                                //$(td +' .tabledit-span').addClass('badge bg-warning');
                                                //$(td).append("<span class='badge bg-warning' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'green');
                                                break;
                                            default:
                                                htmlHeader += '<div class="col-md-4"><span class="badge bg-primary" style="background-color: #49a9df !important;color:#ffffff;">'+item.estado+'</span></div>';
                                                //$(td +' .tabledit-span').addClass('badge bg-primary');
                                                //$(td).append("<span class='badge bg-primary' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'light-blue');
                                                break;
                                        }
                                
                                htmlHeader += '<div class="col-md-4">'+item.cantidad+'</div>';
                                htmlHeader += '</div>';
                            });
                            $("#divHeader").html(htmlHeader);
                            htmlDetail += '<div class="row">';
                            htmlDetail += '<div class="col-md-5">MARCA</div>';
                            htmlDetail += '<div class="col-md-2">FILAS</div>';
                            htmlDetail += '<div class="col-md-3">ESTADO</div>';
                            htmlDetail += '<div class="col-md-2">CANT. ARTIC</div>';
                            htmlDetail += '</div>';
                            $.each(result.dataDetail, function(f, fila) {
                                htmlDetail += '<div class="row">';
                                htmlDetail += '<div class="col-md-5">'+fila.nameMarca+'</div>';
                                htmlDetail += '<div class="col-md-2">'+fila.filas_marca+'</div>';
                                switch (fila.estado_articulo) {
                                            case 'FOUNDED':
                                                //$(td +' .tabledit-span').addClass('badge bg-success');
                                                //$(td).css('margin', 'auto');
                                                //$(td).html("<span class='badge bg-success' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                htmlDetail += '<div class="col-md-3"><span class="badge bg-success" style="background-color: #28a745 !important; color:#ffffff;">'+fila.estado_articulo+'</span></div>';
                                                break;
                                            case 'NOT_FOUND':
                                                htmlDetail += '<div class="col-md-3"><span class="badge bg-danger" style="background-color: #ec0404 !important; color:#ffffff;">'+fila.estado_articulo+'</span></div>';
                                                //$(td +' .tabledit-span').addClass('badge bg-danger');
                                                //$(td).html("<span class='badge bg-danger' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'red');
                                                break;
                                            case 'PARTIAL_FOUND':
                                                htmlDetail += '<div class="col-md-3"><span class="badge bg-warning" style="background-color: #e7af3f !important; color:#ffffff;">'+fila.estado_articulo+'</span></div>';
                                                //$(td +' .tabledit-span').addClass('badge bg-warning');
                                                //$(td).append("<span class='badge bg-warning' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'green');
                                                break;
                                            default:
                                                htmlDetail += '<div class="col-md-3"><span class="badge bg-primary" style="background-color: #49a9df !important; color:#ffffff;">'+fila.estado_articulo+'</span></div>';
                                                //$(td +' .tabledit-span').addClass('badge bg-primary');
                                                //$(td).append("<span class='badge bg-primary' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'light-blue');
                                                break;
                                        }
                                htmlDetail += '<div class="col-md-2">'+fila.cantidad_articulo+'</div>';
                                htmlDetail += '</div>';
                            });

                            $("#divDetail").html(htmlDetail);
                            $('#modal-result').modal('show');
                        }
                }});
        }
    }
    </script>
@stop
