<!DOCTYPE html>
@extends('layouts.master')  

@section('includes')
    @parent
    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::style('lib/bootstrap-multiselect/dist/css/bootstrap-multiselect.css') }}
    {{ HTML::style('http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/css/bootstrapValidator.min.css') }}

    {{ HTML::script('lib/daterangepicker/js/daterangepicker.js') }}
    {{ HTML::script('lib/bootstrap-multiselect/dist/js/bootstrap-multiselect.js') }}
    {{ HTML::script('http://cdnjs.cloudflare.com/ajax/libs/jquery.bootstrapvalidator/0.5.3/js/bootstrapValidator.min.js') }}

    {{ HTML::style('lib/daterangepicker/css/daterangepicker-bs3.css') }}
    {{ HTML::script('//cdn.jsdelivr.net/momentjs/2.9.0/moment.min.js') }}
    {{ HTML::script('lib/daterangepicker/js/daterangepicker_single.js') }}


{{--         <meta name="token" id="token" value="{{ csrf_token() }}"> --}}
        {{ HTML::script('http://cdn.jsdelivr.net/jquery.validation/1.15.1/jquery.validate.js') }}
{{--         {{ HTML::script('https://cdnjs.cloudflare.com/ajax/libs/vue/1.0.24/vue.min.js') }}
        {{ HTML::script('https://cdnjs.cloudflare.com/ajax/libs/vue-resource/0.7.2/vue-resource.min.js') }} --}}
        <script src='https://www.google.com/recaptcha/api.js'></script>

    @include( 'admin.js.slct_global_ajax' )
    @include( 'admin.js.slct_global' )
{{--     @include( 'admin.ruta.js.ruta_ajax' )
    @include( 'admin.ruta.js.validar_ajax' ) --}}
    @include( 'admin.inventario.js.inmueble_ajax' )
    @include( 'admin.inventario.js.inmueble' )
{{--     @include( 'admin.ruta.js.ruta_ajax' ) --}}
@stop
<!-- Right side column. Contains the navbar and content of the page -->
@section('contenido')
<style type="text/css">
.box{
    border: 2px solid #c1c1c1;
    border-radius: 5px;
}
.filtros{
    margin-top: 10px;
    margin-bottom: 0px;
}

.right{
  text-align: right;
}

td, th{
    text-align:center;
}
  
.modal-body label,.modal-body span{
  font-size: 13px;
}

.form-control{
    border-radius: 5px !important;
}
    /*
    Component: Mailbox
*/
.mailbox .table-mailbox {
  border-left: 1px solid #ddd;
  border-right: 1px solid #ddd;
  border-bottom: 1px solid #ddd;
}
.mailbox .table-mailbox tr.unread > td {
  background-color: rgba(0, 0, 0, 0.05);
  color: #000;
  font-weight: 600;
}
.mailbox .table-mailbox .unread
/*.mailbox .table-mailbox tr > td > .fa.fa-ban,*/
/*.mailbox .table-mailbox tr > td > .glyphicon.glyphicon-star,
.mailbox .table-mailbox tr > td > .glyphicon.glyphicon-star-empty*/ {
  /*color: #f39c12;*/
  cursor: pointer;
}
.mailbox .table-mailbox tr > td.small-col {
  width: 30px;
}
.mailbox .table-mailbox tr > td.name {
  width: 150px;
  font-weight: 600;
}
.mailbox .table-mailbox tr > td.time {
  text-align: right;
  width: 100px;
}
.mailbox .table-mailbox tr > td {
  white-space: nowrap;
}
.mailbox .table-mailbox tr > td > a {
  color: #444;
}

.btn-yellow{
    color: #0070ba;
    background-color: ghostwhite;
    border-color: #ccc;
    font-weight: bold;
}

    fieldset{
        max-width: 100% !important;
        border: 3px solid #999;
        padding:10px 20px 2px 20px;
        border-radius: 10px; 
    }

    .margin-top-10{
         margin-top: 10px;   
    }

    .margin-top-5{
      margin-top: 5px;   
    }



@media screen and (max-width: 767px) {
  .mailbox .nav-stacked > li:not(.header) {
    float: left;
    width: 50%;
  }
  .mailbox .nav-stacked > li:not(.header).header {
    border: 0!important;
  }
  .mailbox .search-form {
    margin-top: 10px;
  }
}
</style>

        <!-- Main content -->
        <section class="content">
            <!-- Inicia contenido -->

            <div class="crearInventario">
              <h3>Asignacion en uso de Bienes</h3>

              <form id="FormInventario" method="post">

                <div class="col-md-12 margin-top-10 form-group">
                  <div class="row">
                    <div class="col-md-9">
                      <div class="col-md-2"><span>ENTIDAD: </span></div>
                      <div class="col-md-10">
                        <input type="text" class="form-control" name="txt_entidad" id="txt_entidad" value="MUNICIPALIDAD DISTRITAL DE INDEPENDENCIA" readonly="readonly" style="text-align: center">
                      </div>
                    </div>
                    <div class="col-md-3">
                      <div class="col-md-2"><span>FECHA: </span></div>
                      <div class="col-md-10">
                        <input type="text" class="form-control" name="txt_fecha" id="txt_fecha" value="" readonly="readonly">
                      </div>
                    </div>    
                  </div>
                </div>

                <div class="col-md-12 form-group">
                  <fieldset>
                    <div class="row form-group">
                      <div class="col-md-6">
                        <div class="col-md-4">
                          <span>USUARIO RESPONSABLE: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_userresponsable" id="txt_useresponsable" class="form-control" value="">
                        </div>
                      </div>
                      <div class="col-md-6">
                        <div class="col-md-4">
                           <span>APELLIDOS Y NOMBRES: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_apenomb" id="txt_apenomb" class="form-control" value="">
                        </div> 
                      </div>
                    </div>
                    <div class="row form-group">
                      <div class="col-md-4">
                        <div class="col-md-4">
                          <span>DEPENDENCIA: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_dependencia" id="txt_dependencia" class="form-control" value="">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col-md-4">
                           <span>UBICACION: </span>
                        </div>
                        <div class="col-md-8">
                          <input type="text" name="txt_ubicacion" id="txt_ubicacion" class="form-control" value="">
                        </div> 
                      </div>
                      <div class="col-md-4">
                        <div class="col-md-4">
                          <span>MODALIDAD: </span>
                        </div>
                        <div class="col-md-8">
                           <select class="form-control" id="slct_modalidad" name="slct_modalidad">
                             <option value="">Seleccione modalidad</option>
                             <option value="1">FUNCIONARIO</option>
                             <option value="2">CAP</option>
                             <option value="3">CAS</option>
                           </select>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>

                <div class="col-md-12 form-group">
                  <fieldset>
                    <div class="row">
                      <div class="col-md-4">
                        <div class="col-md-12 form-group">
                          <span>Codigo Patrimonial: </span>
                          <input type="text" name="txt_codpatrimonial" id="txt_codpatrimonial" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Codigo Interno:</span>
                          <input type="text" name="txt_codinterno" id="txt_codinterno" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Descripcion:</span>
                          <textarea class="form-control" name="txt_descripcion" id="txt_descripcion" rows="5"></textarea>
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col-md-12 form-group">
                          <span>Local:</span>
                          <select class="form-control" id="slct_local" name="slct_local">                          
                          </select>
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Oficina:</span>
                          <input type="text" name="txt_oficina" id="txt_oficina" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Marca: </span>
                          <input type="text" name="txt_marca" id="txt_marca" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Modelo: </span>
                          <input type="text" name="txt_modelo" id="txt_modelo" class="form-control">
                        </div>
                      </div>
                      <div class="col-md-4">
                        <div class="col-md-12 form-group">
                          <span>Tipo: </span>
                          <input type="text" name="txt_tipo" id="txt_tipo" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Color: </span>
                          <input type="text" name="txt_color" id="txt_color" class="form-control">
                        </div>
                        <div class="col-md-12 form-group">
                          <span>Serie: </span>
                          <input type="text" name="txt_serie" id="txt_serie" class="form-control">
                        </div>
                         <div class="col-md-12 form-group">
                          <span>Estado:</span>
                          <select class="form-control" id="slct_estado" name="slct_estado">
                            <option value="">Seleccione estado</option>
                            <option value="1">MUY BUENO</option>                          
                            <option value="2">BUENO</option>
                            <option value="3">REGULAR</option>
                            <option value="4">MALO</option>
                          </select>
                        </div>
                      </div>
                    </div>
                  </fieldset>
                </div>
                <div class="col-md-12 form-group" style="text-align: center;">                  
                  <span class="btn btn-success btn-sm" onclick="registrarInmueble()" style="width: 28%"><i class="glyphicon glyphicon-plus"></i> GUARDAR</span>
                {{--   <input type="submit" class="btn btn-primary btn-sm btnAction" id="" value="Guardar" onclick="generarPreTramite()"> --}}
                  {{-- <span class="btn btn-primary btn-sm">CANCELAR</span>              --}}   
                </div>
              </form>

              <div class="col-md-12 form-group hidden">
                <div class="box-body table-responsive">
                  <div class="row form-group" id="reporte" style="">
                      <div class="col-sm-12">
                          <table id="t_reporte" class="table table-bordered table-striped">
                              <thead>
                                  <tr>
                                      <th>Persona</th>
                                      <th>Codigo Patrimonial</th>
                                      <th>Codigo Interno</th>
                                      <th>Descripcion</th>
                                      <th>Local</th>
                                      <th>Oficina</th>
                                      <th>Marca</th>
                                      <th>Modelo</th>
                                      <th>Tipo</th>
                                      <th>Color</th>
                                      <th>Serie</th>
                                      <th>Estado</th>
                                  </tr>
                              </thead>
                              <tbody id="tb_reporte">
                              </tbody>                             
                          </table>
                      </div>
                  </div>
                </div>
              </div>
            </div>

                </div><!-- /.col (RIGHT) -->
            </div>
            <!-- Finaliza contenido -->
        </div>
    </section><!-- /.content -->
@stop
@section('formulario')
  @include( 'admin.ruta.form.crearUsuario' )
  @include( 'admin.ruta.form.crearEmpresa' )
  @include( 'admin.ruta.form.selectPersona' )
  @include( 'admin.ruta.form.buscartramite' )
  @include( 'admin.ruta.form.empresasbyuser' )
  @include( 'admin.ruta.form.rutaflujo' )
  @include( 'admin.ruta.form.ruta' )
@stop