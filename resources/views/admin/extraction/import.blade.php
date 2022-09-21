@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    {{-- <h1>Extracción</h1> --}}
@stop

@section('content')
    <div class="container-fluid">
        <div class="card">
            <div class="card-header">
                <h1>Extracción</h1>
            </div>
            <div class="card-body">
                <form action="{{route('extraccion.importData')}}" id="formUpload" enctype="multipart/form-data" method="post">
                @csrf
                <div class="row" style="width:70%; margin:auto;">
                    <div class="col-12" style="text-align: center;">
                        <img src="{{ asset('images/logo.png') }}" style="max-width:20vw">
                    </div>
                    {{-- <div class="col-md-12">
                        <div class="form-group">
                            <label for="responsable">Responsable</label>
                            <input type="text" id="responsable" name="responsable" class="form-control" placeholder="Por favor ingrese el responsable de esta carga.">
                        </div>
                    </div> --}}
                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="responsable">Importar</label>
                            <input type="hidden" name="MAX_FILE_SIZE" value="31457280" />
                            <input type="file" name="excelin" id="excelin" accept=".xlsx,.xls" class="form-control">
                            <span style="opacity: 0.4">Los archivos aceptados solo son (xls, xlsx)</span>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <button class="form-control btn btn-success" id="btnSubmit" type="submit" role="submit">Procesar</button>
                    </div>
                </div>
                </form>
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
        $("#formUpload").submit(function (e) {
            //stop submitting the form to see the disabled button effect
            e.preventDefault();
            //disable the submit button
            $("#btnSubmit").attr("disabled", true);
            //disable a normal button
            // $("#btnTest").attr("disabled", true);
            var myform = document.getElementById("formUpload");
            let formData = new FormData(myform);
            $.ajax({
                url: "{{ route('extraccion.importData') }}",
                data: formData,
                cache: false,
                processData: false,
                contentType: false,
                type: 'POST',
                beforeSend:function(){
                },
                success: function (response) {
                   console.log("hola");
                }
            });
        });
    });
</script>
@stop
