<script type="text/javascript">

$(document).ready(function() {
    //$("[data-toggle='offcanvas']").click();
    var data = {estado:1};
    var ids = [];
    slctGlobal.listarSlct('flujo','slct_flujo_id','simple',ids,data);
    slctGlobal.listarSlct('area','slct_area_id','simple',ids,data);
    slctGlobal.listarSlct('tiporespuesta','slct_tipo_respuesta','simple',ids,data,0,'#slct_tipo_respuesta_detalle','TR');
    slctGlobal.listarSlct('tiporespuestadetalle','slct_tipo_respuesta_detalle','simple',ids,data,1);
    $("#btn_close").click(cerrar);
    $("#btn_guardar_todo").click(guardarTodo);
    //$("#btn_guardar_todo").click(guardarTodo);
    //$("#areasasignacion").DataTable();
});

validacheck=function(){
    var verboaux="";
    var validacheck=0;
    $("#slct_tipo_respuesta,#slct_tipo_respuesta_detalle").multiselect("enable");
    $("#txt_observacion").removeAttr("disabled");

    $("#t_detalle_verbo input[type='checkbox']").each(
        function( index ) { 
            if ( $(this).is(':checked') ) {
                verboaux+= "|"+$(this).val();
            }
            else{
                validacheck=1;
            }
        }
    );

    if(validacheck==1){
        $("#slct_tipo_respuesta,#slct_tipo_respuesta_detalle").multiselect("disable");
        $("#txt_observacion").attr("disabled","true");
    }
}

cerrar=function(){
    $("#form_ruta_detalle form-group").css("display","none");
    $("#form_ruta_detalle input[type='text'],#form_ruta_detalle textarea").val("");
    $("#form_ruta_detalle t_detalle_verbo").html("");
}

mostrarRutaFlujo=function(){
    $("#form_ruta_detalle>.form-group").css("display","none");
    var flujo_id=$.trim($("#slct_flujo_id").val());
    var area_id=$.trim($("#slct_area_id").val());

    if( flujo_id!='' && area_id!='' ){
        var datos={ flujo_id:flujo_id,area_id:area_id };
        $("#tabla_ruta_detalle").css("display","");
        Validar.mostrarRutaDetalle(datos,mostrarRutaDetalleHTML);
    }
}

mostrarRutaDetalleHTML=function(datos){
    var html="";
    var cont=0;
    var botton="";
    var color="";
    var clase="";
     $('#t_ruta_detalle').dataTable().fnDestroy();
     $("#txt_ruta_detalle_id").remove();
    $.each(datos,function(index,data){
        imagen="";
        clase="";
        cont++;
        if($.trim(data.fecha_inicio)!=''){
            imagen="<a onClick='mostrarDetallle("+data.id+")' class='btn btn-primary btn-sm'><i class='fa fa-edit fa-lg'></i></a>";
        }
    html+="<tr>"+
        "<td>"+cont+"</td>"+
        "<td>"+data.flujo+"</td>"+
        "<td>"+data.area+"</td>"+
        "<td>"+data.software+"</td>"+
        "<td>"+data.id_doc+"</td>"+
        "<td>"+data.norden+"</td>"+
        "<td>"+data.fecha_inicio+"</td>"+
        "<td>"+data.verbo.split("|").join("<br>")+"</td>"+
        "<td>"+imagen+"</td>";
    html+="</tr>";

    });
    $("#tb_ruta_detalle").html(html); 
    $('#t_ruta_detalle').dataTable({
        "ordering": false
    });
}

mostrarDetallle=function(id){
    $("#form_ruta_detalle>.form-group").css("display","");
    var datos={ruta_detalle_id:id}
    Validar.mostrarDetalle(datos,mostrarDetalleHTML);
}

mostrarDetalleHTML=function(datos){
    
    $("#form_ruta_detalle #txt_flujo").val(datos.flujo);
    $("#form_ruta_detalle #txt_area").val(datos.area);
    $("#form_ruta_detalle #txt_software").val(datos.software);
    $("#form_ruta_detalle #txt_id_doc").val(datos.id_doc);
    $("#form_ruta_detalle #txt_orden").val(datos.norden);
    $("#form_ruta_detalle #txt_fecha_inicio").val(datos.fecha_inicio);
    $("#form_ruta_detalle #txt_tiempo").val(datos.tiempo);
    $("#form_ruta_detalle #txt_respuesta").val("-");
    $("#form_ruta_detalle #txt_tipo_respuesta,#form_ruta_detalle #txt_detalle_respuesta").val("");
    $("#form_ruta_detalle #txt_tipo_respuesta,#form_ruta_detalle #txt_detalle_respuesta").multiselect('refresh');

    $("#t_detalle_verbo").html("");
    $("#form_ruta_detalle>#txt_cant_verbo").remove();
    $("#form_ruta_detalle").append("<input type='hidden' id='txt_cant_verbo'>");
    var detalle="";
    var html="";
    var imagen="";
        if ( datos.verbo!='' ) {
            detalle=datos.verbo.split("|");
            html="";
            for (var i = 0; i < detalle.length; i++) {
                imagen = "<i class='fa fa-check fa-lg'></i>";
                if(detalle[i].split("=>")[2]=="Pendiente"){
                    imagen="<input type='checkbox' onChange='validacheck();' value='"+detalle[i].split("=>")[0]+"' name='chk_verbo_"+detalle[i].split("=>")[0]+"' name='chk_verbo_"+detalle[i].split("=>")[0]+"'>";
                }

                html+=  "<tr>"+
                            "<td>"+detalle[i].split("=>")[1]+"<td>"+
                            "<td>"+imagen+"<td>"+
                        "</tr>";
            };
            $("#t_detalle_verbo").html(html);
        }
    validacheck();

}

guardarTodo=function(){
    var verboaux="";
    var contcheck=0;
    var conttotalcheck=0;
    $("#t_detalle_verbo input[type='checkbox']").each(
        function( index ) { 
            if ( $(this).is(':checked') ) {
                verboaux+= "|"+$(this).val();
                contcheck++;
            }
            conttotalcheck++;
        }
    );

    if(conttotalcheck>0){
        verboaux=verboaux.substr(1);
    }

    if( conttotalcheck>0 && contcheck==0 ) {
            alert("Seleccione almenos 1 check");
    }
    else if ( !$("#txt_observacion").attr("disabled") && $("#txt_observacion").val()=='' ) {
        alert("Ingrese una observacion");
    }
    else if ( !$("#slct_tipo_respuesta").attr("disabled") && $("#slct_tipo_respuesta").val()=='' ) {
        alert("Seleccione Tipo de Respuesta");
    }
    else if ( !$("#slct_tipo_respuesta_detalle").attr("disabled") && $("#slct_tipo_respuesta_detalle").val()=='' ) {
        alert("Seleccione Detalle Tipo Respuesta");
    }
    else{
        alert("guardando");
    }
    /*if( $.trim($("#txt_codigo").val())==''){
        alert("Busque y seleccione un código");
    }
    else if( $("#slct_flujo_id").val()=='' ){
        alert("Seleccione un Tipo Flujo");
    }
    else if( $("#slct_area_id").val()=='' ){
        alert("Seleccione una Area");
    }
    else if( !$("#txt_ruta_flujo_id").val() || $("#txt_ruta_flujo_id").val()=='' ){
        alert("Seleccione una combinacion donde almeno exita 1 registro");
    }
    else{
        if ( confirm("Favor de confirmar para registrar") ){
            Validar.guardarAsignacion();
        }
    }*/
}

</script>
