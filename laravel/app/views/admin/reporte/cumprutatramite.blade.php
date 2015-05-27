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
    @include( 'admin.reporte.js.cump_ruta_tramite_ajax' )
    @include( 'admin.reporte.js.cump_ruta_tramite' )
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')

<!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>
            Reporte de Cumplimiento por Ruta
            <small> </small>
        </h1>
        <ol class="breadcrumb">
            <li><a href="#"><i class="fa fa-dashboard"></i> Admin</a></li>
            <li><a href="#">Reporte</a></li>
            <li class="active">Vista de estados de los trámites por Trámite</li>
        </ol>
    </section>

    <!-- Main content -->
    <!-- Main content -->
    <section class="content">
        <div class="box">
            <fieldset>
                <div class="row form-group" id="div_fecha">
                    <div class="col-sm-12">
                        <div class="col-sm-6">
                            <label class="control-label">Proceso:</label>
                            <select class="form-control" name="slct_flujos" id="slct_flujos">
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label">Rango de Fechas:</label>
                            <input type="text" class="form-control" placeholder="AAAA-MM-DD - AAAA-MM-DD" id="fecha" name="fecha" onfocus="blur()"/>
                        </div>
                        <div class="col-sm-3">
                            <label class="control-label"></label>
                            <input type="button" class="form-control btn btn-primary" id="generar" name="generar" value="Mostrar">
                        </div>
                    </div>
                </div>
            </fieldset>
        </div>
        <div class="box-body table-responsive">
            <div class="row form-group" id="reporte" style="display:none;">
                <div class="col-sm-12">
                    <table id="t_reporte" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Código de Tramite</th>
                                <th>Fecha Inicio</th>
                                <th>Mesa Partes</th>
                                <th>Dueño de Proceso</th>
                                <th>Area del Dueño</th>
                                <th># Sin Alerta</th>
                                <th># Alerta</th>
                                <th># Alerta Validada</th>
                                <th> [ ] </th>
                            </tr>
                        </thead>
                        <tbody id="tb_reporte">
                        </tbody>
                        <tfoot>
                            <tr>
                                <th>Código de Tramite</th>
                                <th>Fecha Inicio</th>
                                <th>Mesa Partes</th>
                                <th>Dueño de Proceso</th>
                                <th>Area del Dueño</th>
                                <th># Sin Alerta</th>
                                <th># Alerta</th>
                                <th># Alerta Validada</th>
                                <th> [ ] </th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
<!--         <div class="row form-group">
    <div class="col-sm-12">
        <div id="chart"></div>
    </div>
</div> -->
        <div class="box-body table-responsive" >
            <div class="row form-group" id="reporte_detalle" style="display:none;">
                <div class="col-sm-12">
                    <table id="t_reporteDetalle" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Paso</th>
                                <th>Area del paso</th>
                                <th>Tiempo Asignado</th>
                                <th>Inicio</th>
                                <!-- <th>Cant</th> -->
                                <th>Final</th>
                                <th>Estado Final</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tb_reporteDetalle">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section><!-- /.content -->

@stop
