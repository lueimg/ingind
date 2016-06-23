<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
    @include( 'admin.procesos.js.envioalertas_ajax' )
    @include( 'admin.procesos.js.envioalertas' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
            <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            ENVIO DE NOTIFICACIONES,REITERACIONES Y RELEVOS
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Notificaciones, reiteraciones y relevos</li>
        </ol>
    </section>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->
            <div class="box">
                <fieldset>
                    <div class="row form-group" >
                        <div class="col-sm-12">
                            <div class="col-sm-2">
                                <label class="control-label"></label>
                                <input type="button" class="form-control btn btn-primary" id="enviar" name="enviar" value="Enviar">
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div><!-- /.box -->
            <div class="box-body">
                <div class="col-sm-12">
                    <table id="t_reporte" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Proceso</th>
                                <th>Trámite</th>
                                <th>Paso</th>
                                <th>Tiempo del Paso</th>
                                <th>Fecha de Inicio del Paso</th>
                                <th>Actividad</th>
                                <th>Descripción de la Actividad</th>
                                <th>Área</th>
                                <th>Responsable</th>
                                <th>Recursos</th>
                            </tr>
                        </thead>
                        <tbody id="tb_reporte">
                        </tbody>
                    </table>
                </div>
            </div><!-- /.box -->
            <!-- Finaliza contenido -->
        </div>
    </section><!-- /.content -->
@stop
