<!DOCTYPE html>
@extends('layouts.master')

@section('includes')
    @parent
    {{ HTML::script('lib/ckeditor/ckeditor.js') }}
    {{ HTML::style('css/admin/plantilla.css') }}
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::script('lib/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}
    {{ HTML::script('lib/jquery-bootstrap-validator/bootstrapValidator.min.css') }}
    {{ HTML::script('lib/jquery-bootstrap-validator/bootstrapValidator.min.js') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.mantenimiento.js.docdigital_ajax' )
    @include( 'admin.mantenimiento.js.docdigital' )
    @include( 'admin.mantenimiento.js.docdigitalform' )

@stop
@section('contenido')
    <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                Generar Documentos
                <small> </small>
            </h1>
            <ol class="breadcrumb">
                <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
                <li><a href="#">Mantenimientos</a></li>
                <li class="active">Mis Documentos Digitales</li>
            </ol>
        </section>

        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-xs-12">
                    <!-- Inicia contenido -->
                    <div class="box">
                        <div class="box-header">
                            <h3 class="box-title">Filtros</h3>
                        </div><!-- /.box-header -->
                        <div class="box-body table-responsive">
                            <table id="t_doc_digital" class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 30%">Creador</th>
                                        <th style="width: 30%">Actualizó</th>
                                        <th style="width: 30%">Titulo</th>
                                        <th style="width: 30%">Asunto</th>
                                        <th style="width: 30%">Fecha Creación</th>
                                        <th style="width: 30%">Plantilla</th>
                                       {{--  <th style="width: 19%">Area Recepcion</th>
                                        <th style="width: 19">Persona Recepcion</th> --}}
                                        <th style="width: 5%">Editar</th>
                                        <th style="width: 5%">Vista Previa</th>
                                        <th style="width: 5%">Vista Impresión</th>
                                         <th style="width: 5%">Eliminar</th>
                                    </tr>
                                </thead>
                                <tbody id="tb_doc_digital">
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th style="width: 30%">Creador</th>
                                        <th style="width: 30%">Actualizó</th>
                                        <th style="width: 30%">Titulo</th>
                                        <th style="width: 30%">Asunto</th>
                                        <th style="width: 30%">Fecha Creación</th>
                                        <th style="width: 30%">Plantilla</th>
                        {{--                 <th style="width: 19%">Area Recepcion</th>
                                        <th style="width: 19">Persona Recepcion</th> --}}
                                         <th style="width: 5%">Editar</th>
                                        <th style="width: 5%">Vista Previa</th>
                                        <th style="width: 5%">Vista Impresión</th>
                                        <th style="width: 5%">Eliminar</th>
                                    </tr>
                                </tfoot>
                            </table>

                            <a class='btn btn-success btn-sm' class="btn btn-primary"
                            data-toggle="modal" data-target="#NuevoDocDigital" data-titulo="Nuevo" onclick="Plantillas.CargarAreas();NuevoDocumento();"><i class="fa fa-plus fa-lg"></i>&nbsp;Nuevo</a>
                        </div><!-- /.box-body -->
                    </div><!-- /.box -->
                    <!-- Finaliza contenido -->
                </div>
            </div>

        </section><!-- /.content -->
@stop

@section('formulario')
     @include( 'admin.mantenimiento.form.docdigital' )
     @include( 'admin.mantenimiento.form.editarfechadoc' )
@stop