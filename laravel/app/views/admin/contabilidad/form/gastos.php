<!-- /.modal -->
<div class="modal fade" id="<?=name_controllerG?>Modal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog modal-lg"><!-- modal-lg -->
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body" style="overflow: hidden;">
        <div class="col-md-12">
          <div class="col-md-4" style="padding-top: 15px;">
            <form id="form_<?=name_frmG?>_modal" name="form_<?=name_frmG?>_modal" action="" method="post">
              <div class="form-group">
                <label class="control-label">Proveedor:</label>
                <input type="hidden" class="form-control cls_nhidden" name="txt_contabilidad_gastos_id" id="txt_contabilidad_gastos_id">
                <input type="text" class="form-control" placeholder="Ingrese Nombre" name="txt_proveedor" id="txt_proveedor">
              </div>
              <div class="form-group">
                <label class="control-label">Expediente:</label>
                <input type="text" class="form-control" placeholder="Ingrese Expediente" name="txt_nro_expede" id="txt_nro_expede">
              </div>
              <div class="form-group">
                <label class="control-label">Pago Deuda:</label>
                <input type="text" class="form-control" placeholder="Ingrese Total" name="txt_monto_total" id="txt_monto_total">
              </div>

              <div class="form-group">
                <label class="control-label">Monto Historico:</label>
                <input type="text" class="form-control" placeholder="Ingrese Monto Histo." name="txt_monto_historico" id="txt_monto_historico">
              </div>
            </form>

            <div class="form-group">
              <div class="col-md-4 col-md-offset-2 modal-footer-modi">
                <button type="button" class="btn btn-primary">Guardar</button>
              </div>
              <div class="col-md-2" style="padding-left: 0px;">
                <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
              </div>
            </div>            
          </div>

          <div class="col-md-8">
              <div class="row" style="padding: 5px 15px; text-align: right; padding-top: 0px;">
                <button class="btn btn-default"  id="btnagregar_pago" name="btnagregar_pago"><span class="glyphicon glyphicon-plus-sign" aria-hidden="true"></span> Nuevo</button>
                <button class="btn btn-info"  id="btnguardar_pago" name="btnguardar_pago"><span class="glyphicon glyphicon-floppy-disk" aria-hidden="true"></span> Guardar</button>
              </div>
              <div class="box-body table-responsive">
                <form  method="POST" action="" id="frmgastos_histo" name="frmgastos_histo">
                 <table id="t_gastos_histo" class="table table-bordered">
                    <thead>
                       <tr>
                           <th width="6%">&nbsp;[]</th>
                           <!-- Se agrega campos! -->
                              <th width="12%">GC</th>
                              <th width="12%">GD</th>
                              <th width="12%">GG</th>
                           <!-- -->
                           <th width="10%">Año</th>
                           <th width="12%">C. Conta.</th>
                           <th width="12%">S. Actual</th>
                           <th width="12%">S. Presup.</th>
                           <th width="12%">Creado</th>
                       </tr>
                    </thead>
                    <tbody id="tb_gastos_histo">
                    </tbody>
                 </table>
                </form>
              </div>
          </div>

        </div>

      </div>
    <!-- 
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    -->
    </div>
  </div>
</div>
<!-- /.modal -->




<div id="modalSaldosPagar" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">EXPEDIENTES POR PAGAR</h4>
      </div>
      <div class="modal-body">
        <p>
            <table id="t_gastos_histo" class="table table-bordered">
                <thead>
                   <tr>
                      <th width="30%">Expediente</th>
                      <th width="15%">GC</th>
                      <th width="15%">GD</th>
                      <th width="15%">GG</th>
                      <th width="15%">POR PAGAR</th>
                   </tr>
                </thead>
                <tbody id="tb_saldos_pagar">
                </tbody>
            </table>
        </p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>

  </div>
</div>