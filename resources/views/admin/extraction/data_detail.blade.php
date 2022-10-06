@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>ARCHIVO: {{ $objHeader->description }}</h1>
@stop

@section('content')
        <div class="card">
            <div class="card-header">
                <h3>Listado</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12 dropdown" style="text-align: right;">
                        <input type="hidden" value="{{ $objHeader->id }}" id="idHeader">
                        <a id="my-dropdown" href="#" class="btn btn-primary dropdown-toggle" data-toggle="dropdown">Exportar</a>
                        <ul class="dropdown-menu" style="text-align: left;">
                            <li class="list-group-item"><a href="{{ route('reporte.exportData.excel', ['idHeader' => $objHeader->id])  }}">Reporte Final</a></li>
                            <li class="list-group-item"><a href="{{ route('reporte.exportDataGeneral.excel', ['idHeader' => $objHeader->id])  }}">Reporte General</a></li>
                        </ul>
                    </div>
                    <br>
                    <div class="col-md-12 table-responsive">
                        {{-- <table class="display table table-striped table-hover" id="tableDetail" style="width:100%"> --}}
                        <table class="display table table-striped table-hover nowrap" id="tableDetail" style="width:100%">
                            <thead>
                                <tr>
{{--
                                    <th>ID</th>
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
                                    <th>ESTADO CARGA</th> --}}

                                    <th>ID</th>
                                    <th>DUA</th>
                                    <th>FECHA</th>
                                    <th>ESTADO IMPORTADOR</th>
                                    <th>COD IMPORTADOR</th>
                                    <th>IMPORTADOR</th>
                                    <th>ESTADO PROVEEDOR</th>
                                    <th>CODPROVEEDOR</th>
                                    <th>PROVEEDOR</th>
                                    <th>QTY2</th>
                                    <th>UND2</th>
                                    <th>FOB TOTAL</th>
                                    <th>FOB UND 2</th>
                                    <th>COD PAIS ORIGEN</th>
                                    <th>PAIS ORIGEN</th>
                                    <th>COD PAIS COMPRA</th>
                                    <th>PAIS COMPRA</th>
                                    <th>PUERTO EMBARQUE</th>
                                    <th>DESCRIPCION COMERCIAL</th>
                                    <th>ESTADO ARTICULO</th>
                                    <th>CODFABRICACION</th>
                                    <th>ESTADO MARCA</th>
                                    <th>COD MARCA</th>
                                    <th>NOMBRE MARCA</th>
                                </tr>
                            </thead>
                            <tbody>
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
    <style>

        .dataTables_paginate a {
            padding: 6px 9px !important;
            background: #edf6f9 !important;
            border-color: #b8bdc1 !important;
            border-radius: 5px;
            cursor: pointer;

        }

        /* div.dataTables_wrapper {
            width: 800px;
            margin: 0 auto;
        } */

    </style>
@stop

@section('js')
    <script>
        var editor;
        $(document).ready(function(){
            extraction_cargarDatatablePost();
        });

        function extraction_cargarDatatablePost(){
            var column = 0;
            $('#tableDetail thead th').each( function () {
                column++;
                console.log('filaHeader' + column);
                var title = $(this).text();
                var html = '<div class="row">';
                    html += '<div class="col-12">' + title +'</div>';
                    html += '<div class="col-12"><input type="text" placeholder="Search ' + title + '" /></div>';
                    html += '</div>';
                
                // if (column == 24) { //ultima fila
                //     var html = '<div class="row">';
                //         html += '<div class="col-12">ACCIONES</div>';
                //         html += '</div>';

                // }
                $(this).html(html);
            } );
            
            var table = $('#tableDetail').DataTable({
                            "pageLength": 100,
                            "lengthMenu": [ 100, 250, 500],
                            fixedHeader: {
                                header: true,
                                //footer: true
                            },
                            "processing" : true,
                            "serverSide" : true,
                            //"searching": false,
                            //"bFilter": false,
                            // autoWidth: true,
                            scrollTop: true,
                            scrollY: 400,
                            scrollX: true,
                            processing: true,
                            serverSide: true,
                            ajax: {
                                url: "{{ route('extraccion.data.upload') }}",
                                type: 'POST',
                                data:{
                                idHeader: $('#idHeader').val(),
                                "_token": "{{ csrf_token() }}"
                                }

                            },
                            'order': [],
                            columns: [
                                // { data: 'id', name: 'id',searchable: true },
                                // { data: 'dua', name: 'dua',searchable: true },
                                // { data: 'fecha', name: 'fecha',searchable: true },
                                // { data: 'eta', name: 'eta',searchable: true },
                                // { data: 'importador', name: 'importador',searchable: true },
                                // { data: 'embarcadorExportador', name: 'embarcadorExportador',searchable: true },
                                // { data: 'pesoBruto', name: 'pesoBruto',searchable: true },
                                // { data: 'pesoNeto', name: 'pesoNeto',searchable: true },
                                // { data: 'qty1', name: 'qty1',searchable: true },
                                // { data: 'und2', name: 'und2',searchable: true },
                                // { data: 'qty2', name: 'qty2',searchable: true },
                                // { data: 'und2', name: 'und2',searchable: true },
                                // { data: 'fobTotal', name: 'fobTotal',searchable: true },
                                // { data: 'fobUnd1', name: 'fobUnd1',searchable: true },
                                // { data: 'fobUnd2', name: 'fobUnd2',searchable: true },
                                // { data: 'codPaisOrigen', name: 'codPaisOrigen',searchable: true },
                                // { data: 'paisOrigen', name: 'paisOrigen',searchable: true },
                                // { data: 'codPaisCompra', name: 'codPaisCompra',searchable: true },
                                // { data: 'paisCompra', name: 'paisCompra',searchable: true },
                                // { data: 'puertoEmbarque', name: 'puertoEmbarque',searchable: true },
                                // { data: 'agenteAduanero', name: 'agenteAduanero',searchable: true },
                                // { data: 'estado', name: 'estado',searchable: true },
                                // { data: 'descripcionComercial', name: 'descripcionComercial',searchable: true },
                                // { data: 'marca', name: 'marca',searchable: true },
                                // { data: 'nameMarca', name: 'nameMarca',searchable: true },
                                // { data: 'codigo', name: 'codigo',searchable: true },
                                // { data: 'status', name: 'status',searchable: true }

                                { data: 'id', name: 'id',searchable: true, orderable: false },
                                { data: 'dua', name: 'dua',searchable: true, orderable: false },
                                { data: 'fecha', name: 'fecha',searchable: true, orderable: false },
                                { data: 'statusImporter', name: 'statusImporter',searchable: true, orderable: false,

                                    createdCell: function (td, cellData, rowData, row, col) {
                                        switch (rowData.statusImporter) {
                                            case 'FOUNDED':
                                                $(td).css('color', '#28a745');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-success');
                                                //$(td).css('margin', 'auto');
                                                //$(td).html("<span class='badge bg-success' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                break;
                                            case 'NOT_FOUND':
                                                $(td).css('color', '#ec0404');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-danger');
                                                //$(td).html("<span class='badge bg-danger' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'red');
                                                break;
                                            case 'PARTIAL_FOUND':
                                                $(td).css('color', '#e7af3f');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-warning');
                                                //$(td).append("<span class='badge bg-warning' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'green');
                                                break;
                                            default:
                                                $(td).css('color', '#49a9df');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-primary');
                                                //$(td).append("<span class='badge bg-primary' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'light-blue');
                                                break;
                                        }
                                    }

                                    /*
                                    render: function ( data, type, row ) {
                                        console.log(data);
                                        console.log(type);
                                        console.log(row);
                                        let html = "";
                                        switch (row.statusImporter) {
                                            case 'FOUNDED':
                                                html = "<span class='badge bg-success'>"+data+"</span>";
                                                break;
                                            case 'NOT FOUND':
                                                html = "<span class='badge bg-danger'>"+data+"</span>";
                                                break;
                                            case 'PARTIAL_FOUND':
                                                html = "<span class='badge bg-warning'>"+data+"</span>";
                                                break;
                                            default:
                                                html = "<span class='badge bg-primary'>"+data+"</span>";
                                                break;
                                        }
                                        return html;
                                    }
                                    */

                                },
                                { data: 'codImporter', name: 'codImporter',searchable: true , orderable: false},
                                { data: 'importador', name: 'importador',searchable: true, orderable: false },
                                { data: 'statusProvider', name: 'statusProvider',searchable: true, orderable: false,
                                    createdCell: function (td, cellData, rowData, row, col) {
                                        switch (rowData.statusProvider) {
                                            case 'FOUNDED':
                                                $(td).css('color', '#28a745');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-success');
                                                //$(td).css('margin', 'auto');
                                                //$(td).html("<span class='badge bg-success' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                break;
                                            case 'NOT_FOUND':
                                                $(td).css('color', '#ec0404');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-danger');
                                                //$(td).html("<span class='badge bg-danger' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'red');
                                                break;
                                            case 'PARTIAL_FOUND':
                                                $(td).css('color', '#e7af3f');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-warning');
                                                //$(td).append("<span class='badge bg-warning' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'green');
                                                break;
                                            default:
                                                $(td).css('color', '#49a9df');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-primary');
                                                //$(td).append("<span class='badge bg-primary' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'light-blue');
                                                break;
                                        }
                                    }
                                },
                                { data: 'codProvider', name: 'codProvider',searchable: true , orderable: false},
                                { data: 'embarcadorExportador', name: 'embarcadorExportador',searchable: true , orderable: false },
                                { data: 'qty2', name: 'qty2',searchable: true, orderable: false },
                                { data: 'und2', name: 'und2',searchable: true, orderable: false },
                                { data: 'fobTotal', name: 'fobTotal',searchable: true, orderable: false },
                                { data: 'fobUnd2', name: 'fobUnd2',searchable: true, orderable: false },
                                { data: 'codPaisOrigen', name: 'codPaisOrigen',searchable: true, orderable: false },
                                { data: 'paisOrigen', name: 'paisOrigen',searchable: true, orderable: false },
                                { data: 'codPaisCompra', name: 'codPaisCompra',searchable: true, orderable: false },
                                { data: 'paisCompra', name: 'paisCompra',searchable: true, orderable: false },
                                { data: 'puertoEmbarque', name: 'puertoEmbarque',searchable: true, orderable: false },
                                { data: 'descripcionComercial', name: 'descripcionComercial',searchable: true, orderable: false },
                                { data: 'statusArticle', name: 'statusArticle',searchable: true, orderable: false,
                                    createdCell: function (td, cellData, rowData, row, col) {
                                        switch (rowData.statusArticle) {
                                            case 'FOUNDED':
                                                $(td).css('color', '#28a745');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-success');
                                                //$(td).css('margin', 'auto');
                                                //$(td).html("<span class='badge bg-success' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                break;
                                            case 'NOT_FOUND':
                                                $(td).css('color', '#ec0404');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-danger');
                                                //$(td).html("<span class='badge bg-danger' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'red');
                                                break;
                                            case 'PARTIAL_FOUND':
                                                $(td).css('color', '#e7af3f');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-warning');
                                                //$(td).append("<span class='badge bg-warning' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'green');
                                                break;
                                            default:
                                                $(td).css('color', '#49a9df');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-primary');
                                                //$(td).append("<span class='badge bg-primary' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'light-blue');
                                                break;
                                        }
                                    }
                                },
                                { data: 'codigo', name: 'codigo',searchable: true, orderable: false },
                                { data: 'status', name: 'status',searchable: true, orderable: false,
                                    createdCell: function (td, cellData, rowData, row, col) {
                                        switch (rowData.status) {
                                            case 'FOUNDED':
                                                $(td).css('color', '#28a745');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-success');
                                                //$(td).css('margin', 'auto');
                                                //$(td).html("<span class='badge bg-success' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                break;
                                            case 'NOT_FOUND':
                                                $(td).css('color', '#ec0404');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-danger');
                                                //$(td).html("<span class='badge bg-danger' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'red');
                                                break;
                                            case 'PARTIAL_FOUND':
                                                $(td).css('color', '#e7af3f');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-warning');
                                                //$(td).append("<span class='badge bg-warning' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'green');
                                                break;
                                            default:
                                                $(td).css('color', '#49a9df');
                                                $(td).css('font-weight', 'bold');
                                                //$(td +' .tabledit-span').addClass('badge bg-primary');
                                                //$(td).append("<span class='badge bg-primary' style='background-color: #28a745 !important;'>"+cellData+"</span>")
                                                //$(td).css('background-color', 'light-blue');
                                                break;
                                        }
                                    }
                                },
                                { data: 'marca', name: 'marca',searchable: true, orderable: false },
                                { data: 'nameMarca', name: 'nameMarca',searchable: true, orderable: false },
                                // { data: 'accion', name: 'accion',searchable: false, orderable: false }
                            ],/*
                            "columnDefs":[
                                {
                                    "targets":3,
                                    "sortable":false,
                                    "render": function(data,type,full,meta){
                                        console.log(data);
                                        console.log(type);
                                        console.log(full);
                                        console.log(meta);
                                        let htmlImporter = "";
                                        switch (full.statusImporter) {
                                            case 'FOUNDED':
                                                htmlImporter = "<span class='badge bg-success'>"+full.statusImporter+"</span>";
                                                break;
                                            case 'NOT FOUND':
                                                htmlImporter = "<span class='badge bg-danger'>"+full.statusImporter+"</span>";
                                                break;
                                            case 'PARTIAL_FOUND':
                                                htmlImporter = "<span class='badge bg-warning'>"+full.statusImporter+"</span>";
                                                break;
                                            default:
                                                htmlImporter = "<span class='badge bg-primary'>"+full.statusImporter+"</span>";
                                                break;
                                        }
                                        return htmlImporter;
                                    }
                                },
                                {
                                    "targets":5,
                                    "sortable":false,
                                    "render": function(data,type,full,meta){
                                        let htmlProvider = "";
                                        switch (full.statusProvider) {
                                            case 'FOUNDED':
                                                htmlProvider = "<span class='badge bg-success'>"+full.statusProvider+"</span>";
                                                break;
                                            case 'NOT FOUND':
                                                htmlProvider = "<span class='badge bg-danger'>"+full.statusProvider+"</span>";
                                                break;
                                            case 'PARTIAL_FOUND':
                                                htmlProvider = "<span class='badge bg-warning'>"+full.statusProvider+"</span>";
                                                break;
                                            default:
                                                htmlProvider = "<span class='badge bg-primary'>"+full.statusProvider+"</span>";
                                                break;
                                        }
                                        return htmlProvider;
                                    }
                                },

                            ],*/
                            "select": {
                                style:    'os',
                                selector: 'td:first-child'
                            },
                            initComplete: function () {
                                // Apply the search
                                this.api()
                                    .columns()
                                    .every(function () {
                                        var that = this;
                                        $('input', this.header()).on('keyup change clear', function () {
                                            if (this.value.length > 2 || this.value.length == 0) {
                                                if (that.search() !== this.value) {
                                                    that.search(this.value).draw();
                                                }
                                            }
                                        });
                                    });
                            }
                        });

            table.columns().eq( 0 ).each( function ( colIdx ) {
                $( 'input', table.column( colIdx ).header() ).on( 'keyup change', function () {
                        if (this.value.length > 2 || this.value.length == 0) {
                            table
                            .column( colIdx )
                            .search( this.value )
                            .draw();
                        }
                    });
                } );
            // $('.dataTables_scrollHeadInner table thead tr').append('<th>ACCION</th>');


            $('#tableDetail').on('draw.dt', function(){
                $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') } });
                $('#tableDetail').Tabledit({
                url:"{{ route('extraccion.data.upload.action') }}",
                dataType:'json',
                columns:{
                    identifier : [0, 'id'],
                    editable:[
                        // [1,  'dua'],
                        // [2,  'fecha'],
                        // [3,  'eta'],
                        // [4,  'importador'],
                        // [5,  'embarcadorExportador'],
                        // [6,  'pesoBruto'],
                        // [7,  'pesoNeto'],
                        // [8,  'qty1'],
                        // [9,  'und2'],
                        // [10, 'qty2'],
                        // [11, 'und2'],
                        // [12, 'fobTotal'],
                        // [13, 'fobUnd1'],
                        // [14, 'fobUnd2'],
                        // [15, 'codPaisOrigen'],
                        // [16, 'paisOrigen'],
                        // [17, 'codPaisCompra'],
                        // [18, 'paisCompra'],
                        // [19, 'puertoEmbarque'],
                        // [20, 'agenteAduanero'],
                        // [21, 'estado'],
                        // [22, 'descripcionComercial'],
                        // [23, 'marca'],
                        // [24, 'nameMarca'],
                        // [25, 'codigo'],
                        // [26, 'status']

                        [1,  'dua'],
                        [2,  'fecha'],
                        [3,  'statusImporter'],
                        [4,  'codImporter'],
                        [5,  'importador'],
                        [6,  'statusProvider'],
                        [7,  'codProvider'],
                        [8,  'embarcadorExportador'],
                        [9,  'qty2'],
                        [10,  'und2'],
                        [11, 'fobTotal'],
                        [12, 'fobUnd2'],
                        [13, 'codPaisOrigen'],
                        [14, 'paisOrigen'],
                        [15, 'codPaisCompra'],
                        [16, 'paisCompra'],
                        [17, 'puertoEmbarque'],
                        [18, 'descripcionComercial'],
                        [19, 'statusArticle'],
                        [20, 'codigo'],
                        [21, 'status'],
                        [22, 'marca'],
                        [23, 'nameMarca']
                    ]
                },
                buttons: {
                    edit: {
                        class: 'btn btn-sm btn-default',
                        html: '<i class="fas fa-pencil-alt"></i>',
                        action: 'edit'
                    },
                    delete: {
                        class: 'btn btn-sm btn-default',
                        html: '<i class="fas fa-trash-alt"></i>',
                        action: 'delete'
                    },
                    save: {
                        class: 'btn btn-sm btn-success',
                        html: 'Guardar'
                    },
                    restore: {
                        class: 'btn btn-sm btn-warning',
                        html: 'Cancelar',
                        action: 'restore'
                    }
                },
                restoreButton:false,
                onSuccess:function(data, textStatus, jqXHR)
                {
                    console.log(data);
                    if(data.action == 'delete')
                    {
                    $('#' + data.id).remove();
                    $('#tableDetail').DataTable().ajax.reload();
                    }
                }

                });
            });
            // $('div.dataTables_scrollBody').scrollTop(0);
            // $('.dataTables_scrollHead').attr('style','overflow: auto; position: relative; border: 0px; width: 100%;');
        }
    </script>
@stop
