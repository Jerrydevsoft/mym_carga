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
                    <form action="{{route('extraccion.importBrandData')}}" id="formBrandUpload" enctype="multipart/form-data" method="post" style="width: 100%;">
                        @csrf
                        <div class="row" style="">
                            {{-- <div class="col-md-12">
                                <div class="form-group">
                                    <label for="responsable">Responsable</label>
                                    <input type="text" id="responsable" name="responsable" class="form-control" placeholder="Por favor ingrese el responsable de esta carga.">
                                </div>
                            </div> --}}
                            <div class="col-md-8" style="text-align: left;">
                                <div class="form-group">
                                    <label for="searchVin">Buscar VIN</label>
                                    <input type="text" name="searchVin" id="searchVin" class="form-control">
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
                                    <th>DESCRIPTIONID</th>
                                    <th>COUNTRY</th>
                                    <th>LANGUAJE</th>
                                    <th>DESCRIPTION</th>
                                    <th>FECHA DE REGISTRO</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($data as $fila)
                                    <tr>
                                        <td>{{ $fila->descriptionid}}</td>
                                        <td>{{ $fila->country }}</td>
                                        <td>{{ $fila->language }}</td>
                                        <td>{{ $fila->description }}</td>
                                        <td>{{ $fila->timestamp }}</td>
                                    </tr>
                                @endforeach
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
        // brands_getListBrands();
        $("#formBrandUpload").submit(function (e) {
            //stop submitting the form to see the disabled button effect
            e.preventDefault();
            //disable the submit button
            $("#btnSubmit").attr("disabled", true);
            //disable a normal button
            // $("#btnTest").attr("disabled", true);
            var myform = document.getElementById("formBrandUpload");
            let formData = new FormData(myform);
            $.ajax({
                url: "{{ route('extraccion.importBrandData') }}",
                data: formData,
                async:true,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                dataType:'json',
                beforeSend:function(){
                    Swal.showLoading();
                },
                success: function (response) {
                //    if (response.status == 200) {
                //         window.location.href = "{{ route('extraccion.data')}}";
                //    }else{
                //         window.location.href = "{{ route('extraccion.import')}}";
                //    }
                },
                error:function(objXMLHttpRequest){
                    console.log(objXMLHttpRequest);
                }
            });
        });
    });

    function brands_getListBrands(){
        $.ajax(
                {
                    url: "{{ route('extraccion.import.brand.list') }}",
                    type: 'POST',
                    data:{
                        "_token": "{{ csrf_token() }}"
                    },
                    dataType:'json',
                    success: function(result){
                        console.log(result);
                        console.log(result.status);
                        console.log(result['status']);
                        if (result.status == 200) {
                            html = '';
                            $.each( result.data, function(i,item ) {
                                console.log(item);
                                html+='<tr>';
                                html+='<td>' + item.id + '</td>';
                                html+='<td>' + item.code + '</td>';
                                html+='<td>' + item.name + '</td>';
                                html+='<td>';
                                html+='<div class="form-group">';
                                html+='<div class="custom-control custom-switch">';
                                if (item.status) {
                                    html+='<input type="checkbox" class="custom-control-input" checked id="brandStatus_'+item.id+'" name="brandStatus_'+item.id+'" onchange="brands_changeStatus('+item.id+')">';    
                                }else{
                                    html+='<input type="checkbox" class="custom-control-input" id="brandStatus_'+item.id+'" name="brandStatus_'+item.id+'" onchange="brands_changeStatus('+item.id+')">';
                                }
                                
                                html+='<label class="custom-control-label" for="brandStatus_'+item.id+'"></label>';
                                html+='</div>';
                                // html+='<div class="custom-control custom-switch custom-switch-off-danger custom-switch-on-success">';
                                // html+='<input type="checkbox" class="custom-control-input" id="customSwitch3">';
                                // html+='<label class="custom-control-label" for="customSwitch1"></label>';
                                html+='</div>';
                                //html+='<a href="javascript:void(0);" onclick="users_delete('+item.id+')"><i class="fas fa-trash"></i></a>';
                                html+='</td>';
                                html+='<td>';
                                let ruta = "{{ route('extraccion.import.brand.article', ['idBrand'=>':id'])  }}";
                                ruta = ruta.replace(':id', item.id);
                                html+='<a href="'+ruta+'" title="Ver Articulos"><i class="fas fa-eye" ></i></a>&nbsp;&nbsp;';
                                html+='</td>'
                                html+='</tr>';
                            });
                            $("#tableBrands > tbody").html(html);
                        }
                        brand_datatable();
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

    function brand_datatable(){
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
