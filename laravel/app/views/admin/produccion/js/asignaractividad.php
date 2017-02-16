<script type="text/javascript">
$(document).ready(function() { 
    Area_id = '<?php echo Auth::user()->area_id; ?>';
    id = '<?php echo Auth::user()->id; ?>';
    Rol_id='<?php echo Auth::user()->rol_id; ?>';
    slctGlobal.listarSlctFuncion('area','personaarea','slct_personasA','simple',null,{area_id:Area_id,persona:id});
 

        $(".selectbyPerson").removeClass('hidden');       

    CargarOrden();    
     
    var today = new Date();
                
    initDatePicker();
    initClockPicker();

    $(document).on('change','.clockpicker', function(event) {
        console.log('change');
    });

    // $('.clockpicker').change(function(e){
    //     console.log($(this));
    // });
    
    $(document).on('click', '#btnAdd', function(event) {
        event.preventDefault();
        var template = $(".ordenesT").find('.template-orden').clone().removeClass('template-orden').removeClass('hidden').addClass('valido');
        $(".ordenesT").append(template);
        initDatePicker();
        initClockPicker();
        $("#txt_ttotal").val(CalcGlobalH());
    }); 

    $(document).on('click', '.btnDelete', function(event) {
        $(this).parent().parent().parent().remove();
        initDatePicker();
        initClockPicker();
        $("#txt_ttotal").val(CalcGlobalH());
    }); 


});

function CargarOrden(){
     var dataG = [];
     dataG = {fecha:'<?php echo date("Y-m-d") ?>',tipopersona:'|'};
     Asignar.CargarOrdenTrabajoDia(dataG); 
}
function initClockPicker(){
    $('[data-mask]').inputmask("hh:mm", {
        placeholder: "HH:MM", 
        insertMode: false, 
        showMaskOnHover: false,
        hourFormat: 24
      }
   );
}

function initDatePicker(){
    $('.fechaInicio').datepicker({
        format: 'yyyy-mm-dd',
        language: 'es',
        multidate: 1,
        todayHighlight:true,
        onSelect: function (date, el) {
        }
    })
}

fecha = function(obj,tipo){
    if(typeof (tipo)!='undefined'){
        var row = obj.parentNode.parentNode;
    }else {
      var row = obj.parentNode.parentNode.parentNode.parentNode;  
    }
    var valor =obj.value;

    $(row).find('.fechaFin').val(valor);
    }
    
hora = function(obj,tipo){
    if(typeof (tipo)!='undefined'){
        var row = obj.parentNode.parentNode;
    }else {
      var row = obj.parentNode.parentNode.parentNode.parentNode;  
    }
    var valor =obj.value;

    $(row).find('.horaFin').val(valor);
    }

/*add new verb to generate*/
Addtr = function(e){
    e.preventDefault();
    var template = $(".ordenesT").find('.template-orden').clone().removeClass('template-orden').removeClass('hidden');
    $(".ordenesT").append(template);
    initDatePicker();
    initClockPicker();
}
/*end add new verb to generate*/

/*end delete tr*/
var calcTotal = 0;
CalcularHrs = function(object,tipo){
    if(typeof (tipo)!='undefined'){
        var row = object.parentNode.parentNode;
    }else {
    var row = object.parentNode.parentNode.parentNode.parentNode;
    }
    var HoraInicio = $(row).find('.horaInicio')[0].value;
    var HoraFin = $(row).find('.horaFin')[0].value;


    if(HoraInicio != '' && HoraFin != ''){
        if(HoraInicio < HoraFin){
            var hi = new Date (new Date().toDateString() + ' ' + HoraInicio);
            var hf = new Date (new Date().toDateString() + ' ' + HoraFin);

                var interval = hf.getTime() - hi.getTime();
                calcTotal = calcTotal + interval;
                var hours = ((Math.floor(interval/1000/60/60))%24);
                var min = ((Math.floor(interval/1000/60))%60);
                $(row).find('.ttranscurrido').val(hours + ":" + min);
        }else{
            $(row).find('.horaFin')[0].value = '';
           alert('La hora Inicial ' + HoraInicio +' no puede ser mayor que la final! '+ HoraFin + ', siga el formato hh:mm'); 
        }
    }else{
       
    }
}

CalcGlobalH = function(){
    var calcGlobal=0;
    $(".valido .ttranscurrido").each(function(index, el) {
        var valor = $(el).val();
        if(valor){
            var minutos = parseInt(valor.split(':')[0] * 60) + parseInt(valor.split(':')[1]);
            calcGlobal+=minutos;
        }
    });

    var horas = Math.floor( calcGlobal / 60);
    var min = calcGlobal % 60;
    return horas + ':' + min;
}

guardarTodo = function(){
    var calcG = CalcGlobalH();
    $("#txt_ttotal").val(CalcGlobalH());
    var r = confirm("Usted a generado" + calcG.split(':')[0] + "hora(s) con" + calcG.split(':')[1] + "minuto(s),Desea Guardar?");
    if (r == true) {
        var actividades = $(".valido textarea[id='txt_actividad']").map(function(){return $(this).val();}).get();
        var finicio = $(".valido input[id='txt_fechaInicio']").map(function(){return $(this).val();}).get();
        var ffin = $(".valido input[id='txt_fechaFin']").map(function(){return $(this).val();}).get();
        var hinicio = $(".valido input[id='txt_horaInicio']").map(function(){return $(this).val();}).get();
        var hfin = $(".valido input[id='txt_horaFin']").map(function(){return $(this).val();}).get();
        var ttranscurrido = $(".valido input[id='txt_ttranscurrido']").map(function(){return $(this).val();}).get();
        var persona = document.querySelector("#slct_personasA").value;

        if(actividades.length > 0){
            var data = [];
            var personaid = '';
            if(persona){
                personaid=persona;
            }

            for(var i=0; i < actividades.length;i++){
                if(actividades[i] != '' && finicio[i] != '' && ffin[i] != '' && hfin[i]!='' && hinicio[i]!=''){
                    data.push({
                        'actividad' : actividades[i],
                        'finicio' : finicio[i],
                        'ffin' : finicio[i],
                        'hinicio' : hinicio[i],
                        'hfin' : hinicio[i],
                        'ttranscurrido' : '00:00',
                        'persona':personaid
                    });                    
                }
            }
            if(persona!=''){
            Asignar.guardarOrdenTrabajo(data);
            }else {
                alert("Seleccione al trabajador que se le asignará la(s) actividad(es)");
            }
        }else{
            alert('complete todos los campos porfavor');
        }
    }
};

HTMLcargarordentrabajodia=function(datos){
  var html="";
    
    var alerta_tipo= '';
    $('#t_produccion').dataTable().fnDestroy();
    pos=0;
    $.each(datos,function(index,data){
        var fecha_inicio = data.fecha_inicio.split(' ');
        var dtiempo_final = data.dtiempo_final.split(' ');
        var hinicio = fecha_inicio[1].substring(0, 5);
        var hfin = dtiempo_final[1].substring(0, 5);
        var formato = data.formato.substring(0, 5);
        pos++;
        html+="<tr id="+data.norden+">"+
            "<td>"+pos+'</td>'+
            "<td>"+data.persona+"</td>"+
            "<td>"+data.actividad+"</td>"+
            "<td><input type='text' class='datepicker form-control fechaInicio' id='txt_fechaInicio' name='txt_fechaInicio' onchange='fecha(this,2)' value='"+fecha_inicio[0]+"'></td>"+
            "<td><input type='numeric' class='form-control horaInicio' id='txt_horaInicio' name='txt_horaInicio' onchange='CalcularHrs(this,2)' value='"+hinicio+"' data-mask></td>"+
            "<td align='center'><span class='btn btn-success btn-md' onClick='EditarActividad("+data.norden+","+pos+")' > Editar</a></td>";
        html+="</tr>";
    });
    $("#tb_produccion").html(html);
    initClockPicker();
    initDatePicker();
    $("#t_produccion").dataTable(
             {
            "order": [[ 0, "asc" ],[1, "asc"]],
            "pageLength": 100,
        }
    ); 
  };  

EditarActividad=function(id,pos){
        
//     var finicio = document.getElementById(id).getElementsByTagName('td')[2].innerHTML;
//     var ffin = document.getElementById(id).getElementsByTagName('td')[4].innerHTML;
     var finicio = $('#'+id).find("input:eq(0)").val();     
//     var ffin = $('#'+id).find("input:eq(2)").val();     
     hinicio=$('#'+id).find("input:eq(1)").val();     
//     hfin=$('#'+id).find("input:eq(3)").val();
//     ttranscurrido=$('#'+id).find("input:eq(4)").val();
     var dataG = [];
     dataG = {id:id,finicio:finicio,hinicio:hinicio,ffin:finicio,hfin:hinicio,ttranscurrido:'00:00'};
     Asignar.EditarActividad(dataG,pos);  
    
};



</script>