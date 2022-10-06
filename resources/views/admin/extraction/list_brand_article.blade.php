@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    {{-- <h1>Extracción</h1> --}}
@stop

@section('content')
        <div class="card">
            <div class="card-header">
                <h3>Listado</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <form action="{{route('extraccion.importArticleBrandData')}}" id="formArticleBrand" enctype="multipart/form-data" method="post">
                        @csrf
                        <div class="row" style="">
                            <input type="hidden" id="idBrand" name="idBrand" value="{{$idBrand}}">
                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="responsable">Responsable</label>
                                    <input type="text" id="responsable" name="responsable" class="form-control" placeholder="Por favor ingrese el responsable de esta carga.">
                                </div>
                            </div> --}}
                            <div class="col-md-8" style="text-align: right;">
                                <div class="form-group">
                                    <label for="responsable">Importar</label>
                                    <input type="hidden" name="MAX_FILE_SIZE" value="31457280" />
                                    <input type="file" name="excelin" id="excelin" accept=".xlsx,.xls" class="form-control">
                                    <span style="opacity: 0.4">Los archivos aceptados solo son (xls, xlsx)</span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="form-control btn btn-primary" id="btnSubmit" type="submit" role="submit">Procesar</button>
                            </div>
                        </div>
                    </form>
                </div>
                <br>
                <div class="row" style="width: 100%">
                    <div class="col-md-12 table-responsive" style="height: 800px;">
                        <table class="display table table-striped table-hover" style="width:100%" id="tableBrands">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>FACTORY_CODE</th>
                                    <th>CODIGO</th>
                                    <th>NOMBRE</th>
                                    <th>ESTADO</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
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
        article_getListArticles();

    });

    function article_getListArticles(){
        $.ajax(
                {
                    url: "{{ route('extraccion.import.brand.article.list') }}",
                    type: 'POST',
                    data:{
                        "_token": "{{ csrf_token() }}",
                        idBrand:$("#idBrand").val()
                    },
                    dataType:'json',
                    success: function(result){
                        if (result.status == 200) {
                            html = '';
                            $.each( result.data, function(i,item ) {
                                console.log(item);
                                html+='<tr>';
                                html+='<td>' + item.id + '</td>';
                                html+='<td>' + item.factory_code + '</td>';
                                html+='<td>' + item.code + '</td>';
                                html+='<td>' + item.name + '</td>';
                                html+='<td>' + item.status + '</td>';
                                html+='</tr>';
                            });
                            $("#tableBrands > tbody").html(html);
                        }
                        article_datatable();
                }});
    }

    function users_new(){
        $('#modal-new').modal('show');
    }

    function users_edit(id){
        $.ajax(
                {
                    url: "{{ route('security.users.id') }}",
                    type: 'POST',
                    data:{
                        "_token": "{{ csrf_token() }}",
                        "id":id
                    },
                    dataType:'json',
                    success: function(result){
                        console.log(result);
                        if (result.status) {
                            $('#modal-edit').modal('show');
                            // $('#myModal').modal('hide');
                        }
                }});
    }

    function brands_changeStatus(id){
        var status = 0;
        console.log($('#brandStatus_'+id).is(':checked'));
        if ($('#brandStatus_'+id).is(':checked')) {
            status = 1;
        }
        $.ajax(
                {
                    url: "{{ route('extraccion.import.brand.edit') }}",
                    type: 'POST',
                    data:{
                        "_token": "{{ csrf_token() }}",
                        "id":id,
                        'status':status
                    },
                    dataType:'json',
                    success: function(result){
                        if (result.status) {
                            brands_getListBrands();
                        }
        }});
    }

    function article_datatable(){
        $('#tableBrands').DataTable().destroy();
        var table =  $('#tableBrands').DataTable( {
                    columnDefs: [
                        {
                            targets: [ 0, 1, 2 ],
                            className: 'mdl-data-table__cell--non-numeric'
                        }
                    ]
                } );

                $( table.table().container() ).removeClass( 'form-inline' );
    }

    
    </script>
@stop
