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
                                    <td>{{ $registro->datetimecreated }}</td>
                                    <td>{{ $registro->usrCreated }}</td>
                                    <td>
                                        <a href="{{ route('extraccion.data.revision', ['idHeader' => $registro->id])  }}">revisar</a>
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
@stop

@section('css')
    <link rel="stylesheet" href="/css/admin_custom.css">
@stop

@section('js')
    <script>
    $(document).ready(function(){
        $("#tableHeader").DataTable();
    </script>
@stop
