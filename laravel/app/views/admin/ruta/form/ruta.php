<!-- /.modal -->
<div class="modal fade" id="rutaModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content" style="overflow: auto;height:600px;">
        <!-- Custom Tabs -->
        <div class="nav-tabs-custom">
            <ul class="nav nav-tabs logo">
                <li class="title active">
                  <a href="#tab_verbo" data-toggle="tab">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fa fa-list-ul fa-lg"></i>
                  </button>
                  ASIG. ACCION(ES)
                  </a>
                </li>
                <li class="title">
                  <a href="#tab_tiempo" data-toggle="tab">
                  <button type="button" class="btn btn-default btn-sm">
                    <i class="fa fa-clock-o fa-lg"></i>
                  </button>
                  ASIG. TIEMPO
                  </a>
                </li>
                <li class="pull-right">
                  <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
                    <i class="fa fa-close"></i>
                  </button>
                </li>
            </ul>
            <div class="tab-content">
                <div class="tab-pane" id="tab_tiempo">
                    <form id="form_ruta_tiempo" name="form_areas" action="" method="post">
                      <div class="form-group">
                        <label class="control-label">Area:
                        </label>
                        <input type="text" class="form-control" placeholder="Area" id="txt_nombre" readonly>
                        <select name="slct_tipo_tiempo_modal" id="slct_tipo_tiempo_modal" style="display:none" > 
                          <option value="">.::Seleccione::.</option>
                          <option value="1">Hora</option>
                          <option value="2">Dias</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th>Posicion</th>
                              <th>Tipo Tiempo</th>
                              <th>Tiempo</th>
                            </tr>
                          </thead>
                          <tbody id="tb_tiempo"> </tbody>
                        </table>
                        </div>
                      </div>
                    </form>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button id="btn_guardar_tiempo" type="button" class="btn btn-primary">Guardar</button>
                    </div>
                </div><!-- /.tab-pane -->
                <div class="tab-pane active" id="tab_verbo">
                    <form id="form_ruta_verbo" name="form_areas" action="" method="post">
                      <div class="form-group">
                        <label class="control-label">Area:
                        </label>
                        <input type="text" class="form-control" placeholder="Area" id="txt_nombre" readonly>
                        <select name="slct_condicion_modal" id="slct_condicion_modal" style="display:none" > 
                          <option value="0">No</option>
                          <option value="1">+1</option>
                          <option value="2">+2</option>
                        </select>
                        <select name="slct_rol_modal" id="slct_rol_modal" style="display:none" > 
                          <option value="">Seleccione</option>
                          <option value="1">Secretaria</option>
                          <option value="2">Jefe Area</option>
                          <option value="2">Otros</option>
                        </select>
                        <select name="slct_verbo_modal" id="slct_verbo_modal" style="display:none" > 
                          <option value="">Seleccione</option>
                          <option value="1">Recepcionar</option>
                          <option value="2">Generar</option>
                          <option value="3">Validar</option>
                        </select>
                        <select name="slct_documento_modal" id="slct_documento_modal" style="display:none" > 
                          <option value="">Sin documento</option>
                          <option value="1">Proveido</option>
                          <option value="2">Otros</option>
                        </select>
                      </div>
                      <div class="form-group">
                        <div class="box-body table-responsive">
                        <table class="table table-bordered table-striped">
                          <thead>
                            <tr>
                              <th class="text-center" rowspan="2">Paso</th>
                              <th class="text-center" colspan="4">Acción</th>
                              <th class="text-center" rowspan="2">Condicional</th>
                              <th class="text-center" rowspan="2">[X]</th>
                            </tr>
                            <tr>
                              <th class="text-center">Rol</th>
                              <th class="text-center">Verbo</th>
                              <th class="text-center">Docum. Que Genera</th>
                              <th class="text-center">Descripción</th>
                            </tr>
                          </thead>
                          <tbody id="tb_verbo"> </tbody>
                        </table>
                        </div>
                      </div>
                    </form>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                      <button id="btn_guardar_verbo" type="button" class="btn btn-primary">Guardar</button>
                    </div>
                </div><!-- /.tab-pane -->
            </div><!-- /.tab-content -->
        </div><!-- nav-tabs-custom -->
    </div>
  </div>
</div>
<!-- /.modal -->
