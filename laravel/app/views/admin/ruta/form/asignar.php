<!-- /.modal -->
<div class="modal fade" id="asignarModal" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header logo">
        <button class="btn btn-sm btn-default pull-right" data-dismiss="modal">
            <i class="fa fa-close"></i>
        </button>
        <h4 class="modal-title">Crear Trámite</h4>
      </div>
      <div class="modal-body">
        <form id="form_tabla_relacion" name="form_tabla_relacion" action="" method="post">
          <div class="form-group">
            <label class="control-label">Código Trámite
            </label>
            <input type="text" class="form-control" placeholder="Ingrese Código" name="txt_codigo_modal" id="txt_codigo_modal">
          </div>
          <div class="form-group">
            <label class="control-label">Mesa Partes:
            </label>
            <select class="form-control" name="slct_software_id_modal" id="slct_software_id_modal">
            </select>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="button" onclick="guardarRelacion();" class="btn btn-primary">Guardar</button>
      </div>
    </div>
  </div>
</div>
<!-- /.modal -->
