<!-- /.modal -->
<div class="modal fade" id="flujo_trModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">New message</h4>
      </div>
      <div class="modal-body">
        <form id="form_flujo_tr" name="form_flujo_tr" action="" method="post">
          <div class="form-group">
            <label class="control-label">Flujo:
            </label>
            <select class="form-control" name="slct_flujo_id" id="slct_flujo_id">
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Respuesta:
            </label>
            <select class="form-control" name="slct_tipo_respuesta_id" id="slct_tipo_respuesta_id">
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Tiempo:
            </label>
            <select class="form-control" name="slct_tiempo_id" id="slct_tiempo_id">
            </select>
          </div>
          <div class="form-group">
            <label class="control-label">Cantidad
                <a id="error_dtiempo" style="display:none" class="btn btn-sm btn-warning" data-toggle="tooltip" data-placement="bottom" title="Ingrese Cantidad">
                    <i class="fa fa-exclamation"></i>
                </a>
            </label>
            <input type="text" class="form-control" placeholder="Ingrese Cantidad" name="txt_dtiempo" id="txt_dtiempo">
          </div>
          <div class="form-group">
            <label class="control-label">Estado:
            </label>
            <select class="form-control" name="slct_estado" id="slct_estado">
                <option value='0'>Inactivo</option>
                <option value='1' selected>Activo</option>
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->