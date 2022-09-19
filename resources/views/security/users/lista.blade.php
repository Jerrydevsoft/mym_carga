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
                    <div class="col-md-12" style="text-align: right;">
                        <a href="javascript:void(0);" class="btn btn-primary" onclick="users_new();"><i class="fas fa-plus"></i>NUEVO</a>
                    </div>
                    <div class="col-md-12 table-responsive" style="height: 800px;">
                        <table class="display table table-striped table-hover" style="width:100%" id="tableUsers">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>NOMBRE Y APELLIDO</th>
                                    <th>EMAIL</th>
                                    <th>FECHA DE CREACIÓN</th>
                                    <th>ROL</th>
                                    <th>ACCIONES</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="modal-new" style="display: none;" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">NUEVO USUARIO</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="modal_saveUser" method="post">
                            @csrf
                    
                            {{-- Name field --}}
                            <div class="input-group mb-3">
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" placeholder="{{ __('adminlte::adminlte.full_name') }}" autofocus>
                    
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-user {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                    </div>
                                </div>
                    
                                @error('name')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    
                            {{-- Role field --}}
                            <div class="input-group mb-3">
                                <select type="text" name="role" class="form-control @error('role') is-invalid @enderror"
                                       value="{{ old('role') }}">
                                    <option value="0">::SELECCIONE::</option>
                                    <option value="1">ADMINISTRADOR</option>
                                    <option value="2">ASISTENTE</option>
                                </select>
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                    </div>
                                </div>
                    
                                @error('role')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    
                    
                            {{-- Email field --}}
                            <div class="input-group mb-3">
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                       value="{{ old('email') }}" placeholder="{{ __('adminlte::adminlte.email') }}">
                    
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-envelope {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                    </div>
                                </div>
                    
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    
                            {{-- Password field --}}
                            <div class="input-group mb-3">
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                       placeholder="{{ __('adminlte::adminlte.password') }}">
                    
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                    </div>
                                </div>
                    
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    
                            {{-- Confirm password field --}}
                            <div class="input-group mb-3">
                                <input type="password" name="password_confirmation"
                                       class="form-control @error('password_confirmation') is-invalid @enderror"
                                       placeholder="{{ __('adminlte::adminlte.retype_password') }}">
                    
                                <div class="input-group-append">
                                    <div class="input-group-text">
                                        <span class="fas fa-lock {{ config('adminlte.classes_auth_icon', '') }}"></span>
                                    </div>
                                </div>
                    
                                @error('password_confirmation')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                    
                            {{-- Register button --}}
                            <button type="submit" class="btn btn-block {{ config('adminlte.classes_auth_btn', 'btn-flat btn-primary') }}">
                                <span class="fas fa-user-plus"></span>
                                {{ __('adminlte::adminlte.register') }}
                            </button>
                    
                        </form>
                    </div>
                </div>
            </div> 
        </div>

        <div class="modal fade" id="modal-edit" style="display: none;" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">EDITAR USUARIO</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>One fine body…</p>
                        </div>
                        <div class="modal-footer justify-content-between">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary">Save changes</button>
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
        users_getListUsers();

        $('#modal_saveUser').on('submit',function (e) {
            $.ajax({
                type: 'post',
                url: '{{ route('security.users.save') }}',
                data: $('#modal_saveUser').serialize(),
                dataType:'json',
                success: function(result){
                    if (result.status) {
                        users_getListUsers();
                    }
                }
            });
            e.preventDefault();
        });
    });

    function users_getListUsers(){
        $.ajax(
                {
                    url: "{{ route('security.users.list') }}",
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
                                html+='<td>' + item.name + '</td>';
                                html+='<td>' + item.email + '</td>';
                                html+='<td>' + item.created_at + '</td>';
                                html+='<td>' + item.role + '</td>';
                                html+='<td>';
                                html+='<a href="javascript:void(0);" onclick="users_edit('+item.id+')"><i class="fas fa-pencil-alt"></i></a>&nbsp;';
                                html+='<a href="javascript:void(0);" onclick="users_delete('+item.id+')"><i class="fas fa-trash"></i></a>';
                                html+='</td>';
                                html+='</tr>';
                            });
                            $("#tableUsers > tbody").html(html);
                        }
                        $("#tableUsers").DataTable();
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

    
    </script>
@stop
