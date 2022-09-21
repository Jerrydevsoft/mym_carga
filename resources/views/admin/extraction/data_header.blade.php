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
                                        <a href="javascript:void(0);" onclick="repeatProcess({{$registro->id}})">Procesar</a>
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
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">RESULTADO DE BUSQUEDA HASTA EL MOMENTO</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>One fine body…</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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

    function extraction_showResultExtraction(idHeader){
        if (idHeader > 0) {
            $.ajax(
                {
                    url: "{{ route('extraccion.data.mostrar.result') }}",
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
    </script>
@stop
