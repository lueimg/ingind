<script type="text/javascript">

$(document).ready(function() {
    /*Inicializar tramites*/

    UsuarioId='<?php echo Auth::user()->id; ?>';
    var data={estado:1,persona:UsuarioId};
    Bandeja.MostrarTramites(data,HTMLTramites);
    /*end Inicializar tramites*/

    slctGlobal.listarSlct('documento','cbo_tipodoc','simple',null,data);

    function limpia(area) {
        $(area).find('input[type="text"],input[type="email"],textarea,select').val('');
        $('#FormNuevoAnexo').data('bootstrapValidator').resetForm();
    };

    $('#addAnexo').on('hidden.bs.modal', function(){
        limpia(this);
        $('#spanRuta').addClass('hidden');
    });

    /*validaciones*/
    $('#FormNuevoAnexo').bootstrapValidator({
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh',
        },
        excluded: ':disabled',
        fields: {
            txt_nombreP: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    }
                }
            },
            txt_apeP: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    }
                }
            },
            txt_apeM: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    }
                }
            },
        /*    txt_tipodocP: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    }
                }
            },*/
            txt_numdocP: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    },
                    digits:{
                        message: 'dato numerico'
                    }
                }
            },
            txt_codtramite: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    },
                    digits:{
                        message: 'dato numerico'
                    }
                },
            },
            txt_fechaingreso: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    }
                }
            },
            cbo_tipodoc: {
                validators: {
                    choice: {
                        message: 'selecciona un tipo',
                        min:1
                    }
                }
            },
            txt_nombtramite: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    }
                }
            },
            txt_numdocA: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    },
                    digits:{
                        message: 'dato numerico'
                    }
                }
            },
            txt_folio: {
                validators: {
                    notEmpty: {
                        message: 'campo requerido'
                    },
                    digits:{
                        message: 'dato numerico'
                    }
                }
            }
        }
    });
    /*end validaciones */

    $("form[name='FormNuevoAnexo']").submit(function(e) {
        e.preventDefault();

        $.ajax({
            type: "POST",
            url: 'anexo/create',
            data: new FormData($(this)[0]),
            processData: false,
            contentType: false,
            success: function (obj) {
                if(obj.rst==1){
                   $('#addAnexo').modal('hide');
                }
            }
        });
     });

    $(document).on('click', '#btnImagen', function(event) {
        $('#txt_file').click();
    });

    $(document).on('change', '#txt_file', function(event) {
        readURLI(this,'file');
    });

    function readURLI(input, tipo) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                if (tipo == 'file') {                 
/*                    $('.img-tramite').attr('src',e.target.result);*/
                    $('#spanRuta').text(input.value);
                    $('#spanRuta').removeClass('hidden');
                }
            }
            reader.readAsDataURL(input.files[0]);
        }
    }
});

mostrarTramites = function(){
    var busqueda = document.querySelector('#txtbuscar').value;
    var data ={};
    data.estado = 1;
    if(busqueda){
        data.buscar = busqueda;
    }
    Bandeja.MostrarTramites(data,HTMLTramites);
}

HTMLTramites = function(data){
    if(data.length > 0){
        $("#t_reporte").dataTable().fnDestroy();
        var html ='';
        $.each(data,function(index, el) {
            html+="<tr id="+el.codigo+">"+
                "<td name='codigo'>"+el.codigo +"</td>"+
                "<td name='nombre'>"+el.tramite+"</td>"+
                "<td name='fechaingreso'>"+el.fecha_ingreso+"</td>"+
                "<td name='persona'>"+el.persona+"</td>"+
                "<td name='estado'>"+el.estado+"</td>"+
                "<td name='observacion'>"+el.observacion+"</td>"+
                "<td><span class='btn btn-primary btn-sm' onClick='seleccionado(this),mostrarAnexos(this)'><i class='glyphicon glyphicon-th-list'></i></span></td>"+
                "<td><span class='btn btn-primary btn-sm' idtramite='"+el.codigo+"' onclick='selectTramitetoDetail(this)'><i class='glyphicon glyphicon-search'></i></span></td>"+
            "</tr>";            
        });
        $("#tb_reporte").html(html);
        $("#t_reporte").dataTable(
            {
                "order": [[ 0, "asc" ],[1, "asc"]],
            }
        ); 
        $("#t_reporte").show();
    }else{
        alert('no hay nada');
    }
}

selectTramitetoDetail = function(obj){
    var idtramite = obj.parentNode.parentNode.getAttribute('id');
    var td = document.querySelectorAll("#t_reporte tr[id='"+idtramite+"'] td");
    var data = '{';
    for (var i = 0; i < td.length; i++) {
        if(td[i].getAttribute('name')){
          data+=(i==0) ? '"'+td[i].getAttribute('name')+'":"'+td[i].innerHTML : '","' + td[i].getAttribute('name')+'":"'+td[i].innerHTML;   
        }
    }
    data+='","id":'+idtramite+'}';
    HTMLDetalleTramite(JSON.parse(data));
    $('#estadoTramite').modal('show');
}

HTMLDetalleTramite = function(data){
    document.querySelector('#txtcodtramite').value=data.codigo;
    document.querySelector('#txtfechaIngresado').value=data.fechaingreso;
    document.querySelector('#txtnombtramite').value=data.nombre;
    document.querySelector('#txtdetalle').value=data.observacion;
}

seleccionado = function(obj){
    if(obj){
        var tr = document.querySelectorAll("#t_reporte tr");
        for (var i = 0; i < tr.length; i++) {
            tr[i].setAttribute("style","background-color:#f9f9f9;");
        }
        obj.parentNode.parentNode.setAttribute("style","background-color:#9CD9DE;");
    }
}

mostrarAnexos = function(obj){
    var idtramite = obj.parentNode.parentNode.getAttribute("id");
    var data={'idtramite':idtramite};
    document.querySelector('#txt_idtramite').value=idtramite;
    Bandeja.MostrarAnexos(data,HTMLAnexos);
}

HTMLAnexos = function(data,$tipo_busqueda = ''){
    if(data.length > 0){
        $("#t_anexo").dataTable().fnDestroy();
        var html ='';
        $.each(data,function(index, el) {
            html+="<tr idanexo="+el.codigoanexo+">"+
                "<td name='codigo'>"+el.codigoanexo +"</td>"+
                "<td name='nombre'>"+el.nombreanexo+"</td>"+
                "<td name='fechaingreso'>"+el.fechaingreso+"</td>"+
                "<td name='persona'>"+el.usuarioregistrador+"</td>"+
                "<td name='estado'>"+el.estado+"</td>"+
                "<td name='observacion'>"+el.observacion+"</td>"+
                "<td name='area'>"+el.area+"</td>"+
                "<td><span class='btn btn-primary btn-sm' idanexo='"+el.codigoanexo+"' onclick='selectAnexotoDetail(this)'><i class='glyphicon glyphicon-search'></i></span></td>"+
                "<td><span class='btn btn-primary btn-sm' idanexo='"+el.codigoanexo+"' onclick='selectVoucher(this)'><i class='glyphicon glyphicon-open'></i></span></td>"+
            "</tr>";            
        });
        $("#tb_anexo").html(html);
        $("#t_anexo").dataTable(
            {
                "order": [[ 0, "asc" ],[1, "asc"]],
            }
        ); 
        $("#t_anexo").show();
        var div = document.querySelector(".anexo");
        div.classList.remove("hidden");
    }else{
        if($tipo_busqueda == 'interno'){
            alert('no se encontro anexo');
            $("#tb_anexo").html('');            
        }else{
            alert('no cuenta con anexos');
            var div = document.querySelector(".anexo");
            div.classList.add("hidden");
            $("#tb_anexo").html('');
        }
    }
}

buscarAnexo = function(){
    var busca_anexo = document.querySelector('#txt_anexobuscar').value;
    var id_tramite = document.querySelector('#txt_idtramite').value;
    if(id_tramite){
        var data = {};
        data.estado = 1;
        data.idtramite = id_tramite;

        if(busca_anexo){
            data.buscar = busca_anexo;
        }
        Bandeja.MostrarAnexos(data,HTMLAnexos,'interno');        
    }
}

selectAnexotoDetail = function(obj){
    var idanexo = obj.parentNode.parentNode.getAttribute('idanexo');
    var td = document.querySelectorAll("#t_anexo tr[idanexo='"+idanexo+"'] td");
    var data = '{';
    for (var i = 0; i < td.length; i++) {
        if(td[i].getAttribute('name')){
          data+=(i==0) ? '"'+td[i].getAttribute('name')+'":"'+td[i].innerHTML : '","' + td[i].getAttribute('name')+'":"'+td[i].innerHTML;   
        }
    }
    data+='","id":'+idanexo+'}';
    HTMLDetalleAnexo(JSON.parse(data));
    $('#estadoAnexo').modal('show');
}

HTMLDetalleAnexo = function(data){
    document.querySelector('#txt_anexocodtramite').value=data.prueba;
    document.querySelector('#txt_anexousuariore').value=data.persona;
    document.querySelector('#txt_anexonomtra').value=data.nombre;
    document.querySelector('#txt_anexocod').value=data.id;
    document.querySelector('#txt_anexoarea').value=data.area;
    document.querySelector('#txt_anexofecha').value=data.fechaingreso;
    document.querySelector('#txt_anexoestado').value=data.estado;
    document.querySelector('#txt_anexoobser').value=data.observacion;
}

selectVoucher = function(obj){
    var codanexo = obj.getAttribute('idanexo');
    if(codanexo){
        var data = {estado:1,codanexo:codanexo};
        console.log(data);
        Bandeja.AnexoById(data,HTMLVoucherAnexo);
        $("#voucherAnexo").modal('show');
    }
}

HTMLVoucherAnexo = function(data){
    var result = data[0];
    document.querySelector('#spanvfecha').innerHTML=result.fechaanexo;
    document.querySelector('#spanvncomprobante').innerHTML=result.codanexo;

    document.querySelector('#spanvcodtramite').innerHTML=result.codtramite;

    document.querySelector('#spanvudni').innerHTML=result.dnipersona;
    document.querySelector('#spanvunomb').innerHTML=result.nombrepersona;
    document.querySelector('#spanvuapep').innerHTML=result.apepersona;
    document.querySelector('#spanvuapem').innerHTML=result.apempersona;
    
    if(result.ruc){
        document.querySelector('#spanveruc').innerHTML=result.ruc;
        document.querySelector('#spanvetipo').innerHTML=result.tipoempresa;
        document.querySelector('#spanverazonsocial').innerHTML=result.razonsocial;
        document.querySelector('#spanvenombreco').innerHTML=result.nombcomercial;
        document.querySelector('#spanvedirecfiscal').innerHTML=result.direcfiscal;
        document.querySelector('#spanvetelf').innerHTML=result.etelefono;
        document.querySelector('#spanverepre').innerHTML=result.representantelegal;
        document.querySelector('.vempresa').classList.remove('hidden'); 
    }else{
        document.querySelector('.vempresa').classList.add('hidden'); 
    }

    document.querySelector('#spanvnombtramite').innerHTML=result.nombretramite;
    document.querySelector('#spanFechaTramite').innerHTML=result.fechatramite;
    document.querySelector('#spanArea').innerHTML=result.area;
}
   
</script>