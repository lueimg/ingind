<?php

class CargarController extends BaseController {
    
    public function postCargaproyecto() {
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
        ini_set('max_execution_time',300);
        
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/proyecto';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }

            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            $file = file('txt/proyecto/' . $archivoNuevo);
            //$file=file('/var/www/html/ingind/public/txt/requerimiento/'.$archivoNuevo);
            $usuario_id = 1272;
            $auxArea = '';
            $auxId = '';
            $auxRutaId='';
            $auxRutaFecha='';
            $area_id='';
            for ($i = 0; $i < count($file); $i++) {
 
                DB::beginTransaction();
                if (trim($file[$i]) != '') {
                    $detfile = explode("\t", $file[$i]);

                    for ($j = 0; $j < count($detfile); $j++) {
                        $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                        $reemplazar = "";
                        $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                        $array[$i][$j] = $detfile[$j];
                    }
                    $vartipo = 0; //PERFIL 
                    if (strpos($detfile[3], 'SERVICIO') == false) {
                        $vartipo = 1; // EXPEDIENTE     
                    }
                    // Dar formato a  fechas
                    $fecha = explode('/', $detfile[8]);
                    $nuevaFecha = $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0] . ' 10:00:00';
                    
                 // Encontrar área en procesos
                $area_id = $this->BuscarArea(utf8_encode($detfile[2]));
                $area = Area::find($area_id);

                if (!$area) {
                    $nemonico = 'XX';
                } else {
                    $nemonico = $area->nemonico_doc;
                }
                
                    $tablaRelacion=DB::table('tablas_relacion as tr')
                            ->join(
                                'rutas as r',
                                'tr.id','=','r.tabla_relacion_id'
                            )
                            ->where('tr.id_union', '=', 'REQUERIMIENTO TÉCNICO - N° ' . str_pad($detfile[1], 6, '0', STR_PAD_LEFT) . ' - ' . $detfile[0] . ' - ' . $nemonico)
                            ->where('tr.estado', '=', '1')
                            ->where('r.estado', '=', '1')
                            ->get();


                        if (count($tablaRelacion)==0 and ($detfile[1] != $auxId OR utf8_encode($detfile[2]) != $auxArea) and $detfile[4] != '0') {
                            if($auxRutaId!=''){
                               $rdD= RutaDetalle::where('ruta_id','=',$auxRutaId)
                                                ->where('dtiempo_final','!=','')
                                                ->select(DB::raw('MAX(id) as id'))->first() ; 

                               $idd=$rdD->id+1;
                               $rd= RutaDetalle::find($idd);
                               $rd['dtiempo']=15;
                               $rd['fecha_inicio']='2017-08-10 10:00:00';
                               $rd->save();
                            }
                            $auxId = $detfile[1];
                            $auxArea = utf8_encode($detfile[2]);
                            $auxRutaId='';
                            $auxRutaFecha='';
                            // Encontrar área en procesos
                            $area_id = $this->BuscarArea(utf8_encode($detfile[2]));
                            $area = Area::find($area_id);

                            if (!$area) {
                                $nemonico = 'XX';
                            } else {
                                $nemonico = $area->nemonico_doc;
                            }



                            $tablarelacion = new TablaRelacion;
                            $tablarelacion->software_id = 1;
                            $tablarelacion->id_union = 'REQUERIMIENTO TÉCNICO - N° ' . str_pad($detfile[1], 6, '0', STR_PAD_LEFT) . ' - ' . $detfile[0] . ' - ' . $nemonico;
                            $tablarelacion->sumilla = $detfile[3];
                            $tablarelacion->estado = 1;
                            $tablarelacion->fecha_tramite = $nuevaFecha;
                            $tablarelacion->usuario_created_at = Auth::user()->id;

                            $tablarelacion->save();

                            /* * ************ ENCONTRAR RUTA DE ÁREA *************** */


                            $area_id = $this->BuscarArea(utf8_encode($detfile[2]));

                            if ($area_id == 12) {
                                $rutaFlujo = RutaFlujo::find(4462);}
                            if ($area_id == 25)  {
                                $rutaFlujo = RutaFlujo::find(4461);}
                            
                            $ruta = new Ruta;
                            $ruta['tabla_relacion_id'] = $tablarelacion->id;
                            $ruta['fecha_inicio'] = $nuevaFecha;
                            $ruta['ruta_flujo_id'] = $rutaFlujo->id;
                            $ruta['flujo_id'] = $rutaFlujo->flujo_id;
                            $ruta['persona_id'] = $rutaFlujo->persona_id;
                            $ruta['area_id'] = $rutaFlujo->area_id;
                            $ruta['usuario_created_at'] = Auth::user()->id;
                            $ruta->save();
                            $auxRutaId=$ruta->id;
                            $auxRutaFecha=$ruta->fecha_inicio;

     /*                             * **********Agregado de referidos************ */
                                $referido = new Referido;
                                $referido['ruta_id'] = $ruta->id;
                                $referido['tabla_relacion_id'] = $tablarelacion->id;
                                $referido['tipo'] = 0;
                                $referido['referido'] = $tablarelacion->id_union;
                                $referido['fecha_hora_referido'] = $tablarelacion->created_at;
                                $referido['usuario_referido'] = $tablarelacion->usuario_created_at;
                                $referido['usuario_created_at'] = $usuario_id;
                                $referido->save();

                            $qrutaDetalle = DB::table('rutas_flujo_detalle')
                                    ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                    ->where('estado', '=', '1')
                                    ->orderBy('norden', 'ASC')
                                    ->get();
                       
                            foreach ($qrutaDetalle as $rd) {
                                $rutaDetalle = new RutaDetalle;
                                $rutaDetalle['ruta_id'] = $ruta->id;
                                $rutaDetalle['area_id'] = $rd->area_id;
                                $rutaDetalle['tiempo_id'] = $rd->tiempo_id;
                                $rutaDetalle['dtiempo'] = $rd->dtiempo;
                                $rutaDetalle['norden'] = $rd->norden;
                                $rutaDetalle['estado_ruta'] = $rd->estado_ruta;

                                $rutaDetalle['usuario_created_at'] = Auth::user()->id;
                                $rutaDetalle->save();

                                if ($rutaDetalle->norden == 1) {
                                    $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                    $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                    $rutaDetalle['tipo_respuesta_id'] = 1;
                                    $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                    $rutaDetalle['observacion'] = '';
                                    $rutaDetalle->save();
                                }

                                $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                                        ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                        ->where('estado', '=', '1')
                                        ->orderBy('orden', 'ASC')
                                        ->get();
                                if (count($qrutaDetalleVerbo) > 0) {
                                    foreach ($qrutaDetalleVerbo as $rdv) {
                                        $rutaDetalleVerbo = new RutaDetalleVerbo;
                                        $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                                        $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                                        $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                                        $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                                        $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                                        $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                                        $rutaDetalleVerbo['orden'] = $rdv->orden;
                                        $rutaDetalleVerbo['usuario_created_at'] = Auth::user()->id;
                                        $rutaDetalleVerbo->save();
                                    }
                                }
                                if($rutaDetalle->norden == 1){

                                $rutaDetalleVerbo = RutaDetalleVerbo::where('ruta_detalle_id', '=', $rutaDetalle->id)
                                                ->where('estado', '=', 1)->get();
                                foreach ($rutaDetalleVerbo as $r) {
                                    $rdv = RutaDetalleVerbo::find($r->id);
                                    if ($rdv->verbo_id == 1 and utf8_encode($detfile[7])!='') {
                                        $rdv['documento'] = utf8_encode($detfile[7]);
                                    }
                                    $rdv['finalizo'] = 1;
                                    $rdv['observacion'] = 'AUTOMATICO';
                                    $rdv['usuario_created_at'] = 1272;
                                    $rdv['usuario_updated_at'] = 1272;
                                    $rdv['updated_at'] = $nuevaFecha;
                                    $rdv->save();
                                }
                                }
                            }
                            
                            if($detfile[4]!=1){
                             $i--;
                            }
                        } 
                        else {
    //                        $fecha = explode('/', $detfile[9]);
    //                        $nuevaFecha = $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0] . ' 08:00:00';
    
                            $rutaDetalle = array();
                            $auxRutaFecha=$nuevaFecha;
                            if ($detfile[4]*1 == 2) {
                                $varposicion=2;
                                if($area_id==26){
                                    $varposicion = 1;
                                }

                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '3') {
                                $varposicion=5;
                                if($area_id==26){
                                    $varposicion = 2;
                                }
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '4') {
                                $varposicion=6;
//                                if ($area_id == 26) {
//                                    $varposicion=3;
//                                }
                                if (utf8_encode($detfile[5]) == 'Gerencia de Planificación, Presupuesto y Racionalización') {

                                    $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                    ->where('ruta_id', '=', $ruta->id)->first();
                                    $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                    $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                    $rutaDetalle['tipo_respuesta_id'] = 1;
                                    $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                    $rutaDetalle['observacion'] = '';
                                    $rutaDetalle->save();
                                }else {

                                        $Ssql="SELECT MAX(id) as id "
                                                . "FROM referidos "
                                                . "WHERE ruta_id=".$ruta->id;
                                        $refe=DB::select($Ssql);

                                        $referido = Referido::find($refe[0]->id);
                                        $referido['referido'] = utf8_encode($detfile[7]).'|'.$referido->referido;
                                        $referido->save();


                                }
                            }

                            if ($detfile[4] == '5') {
                                $varposicion=6;
                            /*    if ($area_id == 26) {
                                    $varposicion=3;
                                }*/
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '6' or $detfile[4] == '8') {

                                $varposicion = 13;
//                                if($detfile[4] == '8'){
//                                    $varposicion=13;
//                                }
                               /* if($area_id==26){
                                    $varposicion = 6;
                                }
                                if($area_id==29){
                                    $varposicion = 5;
                                }
                                if ($vartipo == 1) {
                                     if($area_id==29 or $area_id==38 or $area_id==26){
                                        $varposicion = 7;
                                     }else{
                                        $varposicion = 8;
                                     }
                                }*/
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '7') {

                                $varposicion = 14;
                           /*     if($area_id==26){
                                    $varposicion = 7;
                                }
                                if($area_id==29){
                                    $varposicion = 6;
                                }
                                if ($vartipo == 1) {
                                      if($area_id==29 or $area_id==38 or $area_id==26){
                                          $varposicion = 8;
                                      }else{
                                          $varposicion = 9;

                                      }
                                }*/
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if (count($rutaDetalle) > 0) {
                                $rutaDetalleVerbo = RutaDetalleVerbo::where('ruta_detalle_id', '=', $rutaDetalle->id)
                                                ->where('estado', '=', 1)->get();
                                foreach ($rutaDetalleVerbo as $r) {
                                    $rdv = RutaDetalleVerbo::find($r->id);
                                    if ($rdv->verbo_id == 1 and utf8_encode($detfile[7])!='') {
                                        $rdv['documento'] = utf8_encode($detfile[7]);
                                         /** **********Agregado de referidos************ */
                                        $referido = new Referido;
                                        $referido['ruta_id'] = $ruta->id;
                                        $referido['tabla_relacion_id'] = $tablarelacion->id;
                                        $referido['tipo'] = 1;
                                        $referido['ruta_detalle_id'] = $rutaDetalle->id;
                                        $referido['norden'] = $rutaDetalle->norden;
                                        $referido['estado_ruta'] = $rutaDetalle->estado_ruta;
                                        $referido['referido'] = utf8_encode($detfile[7]);
                                        $referido['ruta_detalle_verbo_id'] = $rdv->id;
                                        $referido['fecha_hora_referido'] = $nuevaFecha;
                                        $referido['usuario_referido'] = $usuario_id;
                                        $referido['usuario_created_at'] = $usuario_id;
                                        $referido->save();

                                    }
                                    $rdv['finalizo'] = 1;
                                    $rdv['observacion'] = 'AUTOMATICO';
                                    $rdv['usuario_created_at'] = $usuario_id;
                                    $rdv['usuario_updated_at'] = $usuario_id;
                                    $rdv['updated_at'] = $nuevaFecha;
                                    $rdv->save();
                                }
                            }
                        }

                }
                DB::commit();
            }// for del file
            //exit;
                if($auxRutaId){
                               $rdD= RutaDetalle::where('ruta_id','=',$auxRutaId)
                                                ->where('dtiempo_final','!=','')
                                                ->select(DB::raw('MAX(id) as id'))->first() ; 

                               $idd=$rdD->id+1;
                               $rd= RutaDetalle::find($idd);
                               $rd['dtiempo']=15;
                               $rd['fecha_inicio']='2017-08-10 10:00:00';
                               $rd->save();
                }
               
                        
            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                //'data'      => $array,
                                'data' => array(),
                                'existe' => 0//$arrayExist
                            )
            );
        }
    }
    
    public function postAsignacionsistradocvalida() {
        ini_set('memory_limit', '512M');
        set_time_limit(600);
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/asignacion';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }


            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            //$file=file('C:\\wamp\\www\\ingind\\public\\txt\\asignacion\\'.$archivoNuevo);
            //$file=file('/home/m1ndepen/public_html/procesosmuni/public/txt/asignacion/'.$archivoNuevo);

            $file = file('/var/www/html/ingind/public/txt/asignacion/' . $archivoNuevo);

            $tipoTramite['01'] = 'EN TRAMITE';
            $tipoTramite['02'] = 'ANULADO';
            $tipoTramite['03'] = 'RESUELTO';
            $tipoTramite['04'] = 'ARCHIVADO';
            $tipoTramite['05'] = 'CUSTODIA';
            $tipoTramite['06'] = '1º RESOLUCION ( ATENDIDO )';
            $tipoTramite['07'] = '2º RESOLUCIÓN ( RESOL. RECONSD.)';
            $tipoTramite['08'] = '3º RESOLUCION ( RESOL. APELACION )';
            $tipoTramite['09'] = '4º RESOLUCION ( ABANDONO )';
            $tipoTramite['10'] = '5º RESOLUCION ( RECONS. ABANDNO )';
            $tipoTramite['11'] = '6º RESOLUCION ( APELAC. ABANDONO )';
            $tipoTramite['12'] = 'FORMULARIOS';
            $tipoTramite['13'] = 'DOCUMENTACION INTERNA';
            $tipoTramite['14'] = 'ENVIAR EL ANEXO AL EXPEDIENTE ORIGINAL';
            $tipoTramite['15'] = 'ATENDIDO';
            $tipoTramite['16'] = 'EN PROCESO';
            for ($i = 0; $i < count($file); $i++) {
                $detfile = explode("\t", $file[$i]);

                for ($j = 0; $j < count($detfile); $j++) {
                    $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                    $reemplazar = "";
                    $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                    $array[$i][$j] = $detfile[$j];
                }

                //if($i>0){
                $exist = TablaRelacion::where('id_union', '=', $detfile[0])
                        ->where('estado', '=', '1')
                        ->get();

                $ainterna = AreaInterna::find($detfile[12]);
                $tdoc = explode("-", $detfile[0]);

                if (count($ainterna) == 0 AND count($exist) > 0) {
                    $sql = "  SELECT rd.area_id
                                    FROM rutas r
                                    INNER JOIN rutas_detalle rd ON rd.ruta_id=r.id AND rd.estado=1
                                    where rd.norden=2
                                    AND r.estado=1 
                                    AND r.tabla_relacion_id='" . $exist[0]->id . "'";
                    $areaId = DB::select($sql);

                    $ainterna = AreaInterna::where('area_id', '=', $areaId[0]->area_id)
                            ->where('estado', '=', '1')
                            ->firts();
                }

                if (count($ainterna) == 0) {
                    $arrayExist[] = $detfile[0] . "; No cuenta con Ruta revise cod area de plataforma ingresado.";
                } else {

                    if (count($exist) != 1) {
                        if (count($exist) == 0) {
                            $arrayExist[] = $detfile[0] . "; Tramite no existe. Estado: " . $tipoTramite[$detfile[15]] . " Cant: " . $detfile[16];
                        } elseif (count($exist) > 1) {
                            $arrayExist[] = $detfile[0] . "; Tramite ya existe. Estado: " . $tipoTramite[$detfile[15]] . " Cant: " . $detfile[16];
                        }
                    } elseif ($detfile[16] <= 2) {
                        $arrayExist[] = $detfile[0] . "; Tramite no fué atendido por Sistradoc. Estado: " . $tipoTramite[$detfile[15]] . " Cant:" . $detfile[16];
                    } else {

                        $tipoPersona = TipoSolicitante::where('nombre_relacion', '=', $detfile[2])->first();
                        if (count($tipoPersona) == 0) {
                            $arrayExist[] = $detfile[0] . "; TipoPersona no existe. " . $tipoTramite[$detfile[15]] . " Cant: " . $detfile[16];
                        } else {
                            DB::beginTransaction();

                            $tr = new TablaRelacion;
                            $tr['tipo_persona'] = $tipoPersona->id;

                            if ($detfile[3] != "") { // razon social
                                $tr['razon_social'] = $detfile[3];
                            }

                            if ($detfile[4] != "") { // ruc
                                $tr['ruc'] = $detfile[4];
                            }

                            if ($detfile[5] != "") { // dni
                                $tr['dni'] = $detfile[5];
                            }

                            if ($detfile[6] != "") { // paterno
                                $tr['paterno'] = $detfile[6];
                            }

                            if ($detfile[7] != "") { // materno
                                $tr['materno'] = $detfile[7];
                            }

                            if ($detfile[8] != "") { // nombre
                                $tr['nombre'] = $detfile[8];
                            }

                            $fecha_inicio = date("Y-m-d 08:00:00");

                            $tr['software_id'] = '1';
                            $tr['id_union'] = $detfile[0];
                            $tr['fecha_tramite'] = $fecha_inicio;
                            $tr['sumilla'] = $detfile[9];
                            $tr['email'] = $detfile[10];
                            $tr['telefono'] = $detfile[11];
                            $tr['usuario_created_at'] = 1272;
                            $tr->save();

                            $flujo_interno = trim($detfile[13]);
                            $rf = array('');

                            $rf = RutaFlujo::where('flujo_id', '=', $ainterna->flujo_id)
                                    ->where('estado', '=', '1')
                                    ->first();

                            $rutaFlujo = RutaFlujo::find($rf->id);


                            $ruta = new Ruta;
                            $ruta['tabla_relacion_id'] = $tr->id;
                            $ruta['fecha_inicio'] = $fecha_inicio;
                            $ruta['ruta_flujo_id'] = $rutaFlujo->id;
                            $ruta['flujo_id'] = $rutaFlujo->flujo_id;
                            $ruta['persona_id'] = $rutaFlujo->persona_id;
                            $ruta['area_id'] = $rutaFlujo->area_id;
                            $ruta['usuario_created_at'] = 1272;
                            $ruta->save();

                            /*                             * **********Agregado de referidos************ */
                            $referido = new Referido;
                            $referido['ruta_id'] = $ruta->id;
                            $referido['tabla_relacion_id'] = $tr->id;
                            $referido['tipo'] = 0;
                            $referido['referido'] = $tr->id_union;
                            $referido['fecha_hora_referido'] = $tr->created_at;
                            $referido['usuario_referido'] = $tr->usuario_created_at;
                            $referido['usuario_created_at'] = 1272;
                            $referido->save();
                            /*                             * ******************************************* */

                            $qrutaDetalle = DB::table('rutas_flujo_detalle')
                                    ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                    ->where('estado', '=', '1')
                                    ->orderBy('norden', 'ASC')
                                    ->get();
                            $validaactivar = 0;
                            foreach ($qrutaDetalle as $rd) {
                                $cero='';
                                if($rd->norden<10){
                                    $cero='0';
                                }
                                $rutaDetalle = new RutaDetalle;
                                $rutaDetalle['ruta_id'] = $ruta->id;
                                $rutaDetalle['area_id'] = $rd->area_id;
                                $rutaDetalle['tiempo_id'] = $rd->tiempo_id;
                                $rutaDetalle['dtiempo'] = $rd->dtiempo;
                                $rutaDetalle['norden'] =  $cero.$rd->norden;
                                $rutaDetalle['estado_ruta'] = $rd->estado_ruta;

                                $rutaDetalle['dtiempo_final'] = date('Y-m-d 08:00:00');
                                $rutaDetalle['tipo_respuesta_id'] = 2;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = "";
                                $rutaDetalle['usuario_updated_at'] = 1272;
                                $rutaDetalle['updated_at'] = date('Y-m-d 08:00:00');
                                $rutaDetalle['fecha_inicio'] = $fecha_inicio;

                                $rutaDetalle['usuario_created_at'] = 1272;
                                $rutaDetalle->save();

                                $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                                        ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                        ->where('estado', '=', '1')
                                        ->orderBy('orden', 'ASC')
                                        ->get();
                                if (count($qrutaDetalleVerbo) > 0) {
                                    foreach ($qrutaDetalleVerbo as $rdv) {
                                        $rutaDetalleVerbo = new RutaDetalleVerbo;
                                        $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                                        $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                                        $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                                        $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                                        $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                                        $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                                        $rutaDetalleVerbo['orden'] = $rdv->orden;
                                        $rutaDetalleVerbo['usuario_created_at'] = 1272;

                                        $rutaDetalleVerbo['usuario_updated_at'] = 1272;
                                        $rutaDetalleVerbo['updated_at'] = date('Y-m-d 08:00:00');
                                        $rutaDetalleVerbo['finalizo'] = 1;

                                        $rutaDetalleVerbo->save();
                                    }
                                }
                            }
                            DB::commit();
                        }
                    } //es codigo nuevo
                }// valida si tiene flujo id
                //}// Apartir del 2 registro
            }// for del file

            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                'data' => $array,
                                'existe' => $arrayExist
                            )
            );
        }
    }

    public function postCargarinventario() {
        ini_set('memory_limit', '512M');
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/inventario';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }


            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            /*            $file=file('txt/inventario/'.$archivoNuevo); */
            $file = file('/var/www/html/ingind/public/txt/inventario/' . $archivoNuevo);
            for ($i = 0; $i < count($file); $i++) {
                DB::beginTransaction();
                if (trim($file[$i]) != '') {
                    $detfile = explode("\t", $file[$i]);


                    for ($j = 0; $j < count($detfile); $j++) {
                        $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                        $reemplazar = "";
                        $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                        $array[$i][$j] = $detfile[$j];
                    }
                    /* validar existencia */
                    $exist = Inmueble::where('cod_patrimonial', '=', $detfile[0])
                            ->where('cod_interno', '=', $detfile[1])
                            ->where('estado', '=', '1')
                            ->get();

                    if (count($exist) > 0) {
                        $arrayExist[] = $detfile[0] . "; Inmueble ya existe";
                    } else {
                        /* registro datos */

                        $Inmueble = new Inmueble();
                        $Inmueble['cod_patrimonial'] = $detfile[0];
                        $Inmueble['cod_interno'] = $detfile[1];
                        $Inmueble['descripcion'] = $detfile[2];

                        $persona = DB::table('personas')
                                ->where('dni', '=', $detfile[9])
                                ->get();
                        $Inmueble['area_id'] = $persona[0]->area_id;
                        $Inmueble['persona_id'] = $persona[0]->id;


                        $local = DB::table('inventario_local')
                                ->where('nombre', 'LIKE', '%' . $detfile[11] . '%')
                                ->get();
                        $Inmueble['inventario_local_id'] = $local[0]->id;

                        if ($detfile[10] != '') {
                            if ($detfile[10] == 'CAS') {
                                $Inmueble['modalidad_id'] = 3;
                            } elseif ($detfile[10] == 'CAP') {
                                $Inmueble['modalidad_id'] = 2;
                            } else {
                                $Inmueble['modalidad_id'] = 1;
                            }
                        }

                        if ($detfile[12] != '') {
                            $Inmueble['oficina'] = $detfile[12];
                        }

                        if ($detfile[3] != '') {
                            $Inmueble['marca'] = $detfile[3];
                        }

                        if ($detfile[4] != '') {
                            $Inmueble['modelo'] = $detfile[4];
                        }

                        if ($detfile[5] != '') {
                            $Inmueble['tipo'] = $detfile[5];
                        }

                        $Inmueble['color'] = $detfile[6];
                        $Inmueble['serie'] = $detfile[7];
                        $Inmueble['fecha_creacion'] = date('Y-m-d', strtotime($detfile[13]));
                        $Inmueble['situacion'] = $detfile[8];
                        $Inmueble['created_at'] = date('Y-m-d H:i:s');
                        $Inmueble['usuario_created_at'] = Auth::user()->id;
                        $Inmueble->save();
                        /* end registros datos */
                    }
                    /* end validar existencia */
                }
                DB::commit();
            }// for del file
            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                'data' => $array,
                                'existe' => $arrayExist
                            )
            );
        }
    }

    public function postAsignacion() {
        ini_set('memory_limit', '512M');
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/asignacion';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }


            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            /* $file=file('txt/asignacion/'.$archivoNuevo); */
            /* $file=file('C:\\xampp\\www\\htdocs\\ingind\\public\\txt\\asignacion\\'.$archivoNuevo); */
            //$file=file('/home/m1ndepen/public_html/procesosmuni/public/txt/asignacion/'.$archivoNuevo);

            $file = file('/var/www/html/ingind/public/txt/asignacion/' . $archivoNuevo);
            $tipoTramite['01'] = 'EN TRAMITE';
            $tipoTramite['02'] = 'ANULADO';
            $tipoTramite['03'] = 'RESUELTO';
            $tipoTramite['04'] = 'ARCHIVADO';
            $tipoTramite['05'] = 'CUSTODIA';
            $tipoTramite['06'] = '1º RESOLUCION ( ATENDIDO )';
            $tipoTramite['07'] = '2º RESOLUCIÓN ( RESOL. RECONSD.)';
            $tipoTramite['08'] = '3º RESOLUCION ( RESOL. APELACION )';
            $tipoTramite['09'] = '4º RESOLUCION ( ABANDONO )';
            $tipoTramite['10'] = '5º RESOLUCION ( RECONS. ABANDNO )';
            $tipoTramite['11'] = '6º RESOLUCION ( APELAC. ABANDONO )';
            $tipoTramite['12'] = 'FORMULARIOS';
            $tipoTramite['13'] = 'DOCUMENTACION INTERNA';
            $tipoTramite['14'] = 'ENVIAR EL ANEXO AL EXPEDIENTE ORIGINAL';
            $tipoTramite['15'] = 'ATENDIDO';
            $tipoTramite['16'] = 'EN PROCESO';
            for ($i = 0; $i < count($file); $i++) {
                $detfile = explode("\t", $file[$i]);

                for ($j = 0; $j < count($detfile); $j++) {
                    $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                    $reemplazar = "";
                    $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                    $array[$i][$j] = $detfile[$j];
                }

                //if($i>0){
                $ainterna = AreaInterna::find($detfile[12]);
                $tdoc = explode("-", $detfile[0]);

                if (count($ainterna) == 0) {
                    $arrayExist[] = $detfile[0] . "; No cuenta con Ruta revise cod area de plataforma ingresado.";
                }
//                        elseif( strtoupper(substr($ainterna->nombre,0,3))=='SUB' AND 
//                                ( strtoupper($tdoc[0])=='DS' OR strtoupper($tdoc[0])=='EX' ) AND
//                                $ainterna->id!=20 AND $ainterna->id!=23 AND $ainterna->id!=29 AND 
//                                $ainterna->id!=36 AND $ainterna->id!=40
//                        )
//                        {
//                            $arrayExist[]=$detfile[0]."; No se puede ingresar el tipo de tramite DS ni EX para sub gerencias";
//                        }
                else {
                    $exist = TablaRelacion::where('id_union', '=', $detfile[0])
                            ->where('estado', '=', '1')
                            ->get();

                    if (count($exist) > 0) {
                        $arrayExist[] = $detfile[0] . "; Tramite ya existe";
                    } elseif ($tipoTramite[$detfile[15]] == 'ANULADO') {
                        $arrayExist[] = $detfile[0] . "; No se puede ingresar trámite anulado";
                    } else {

                        $tipoPersona = TipoSolicitante::where('nombre_relacion', '=', $detfile[2])->first();
                        if (count($tipoPersona) == 0) {
                            $arrayExist[] = $detfile[0] . "; TipoPersona no existe";
                        } else {
                            DB::beginTransaction();

                            $tr = new TablaRelacion;
                            $tr['tipo_persona'] = $tipoPersona->id;

                            if ($detfile[3] != "") { // razon social
                                $tr['razon_social'] = $detfile[3];
                            }

                            if ($detfile[4] != "") { // ruc
                                $tr['ruc'] = $detfile[4];
                            }

                            if ($detfile[5] != "") { // dni
                                $tr['dni'] = $detfile[5];
                            }

                            if ($detfile[6] != "") { // paterno
                                $tr['paterno'] = $detfile[6];
                            }

                            if ($detfile[7] != "") { // materno
                                $tr['materno'] = $detfile[7];
                            }

                            if ($detfile[8] != "") { // nombre
                                $tr['nombre'] = $detfile[8];
                            }

                            $fecha_inicio = date("Y-m-d H:i:s");

                            $tr['software_id'] = '1';
                            $tr['id_union'] = $detfile[0];
                            $tr['fecha_tramite'] = $fecha_inicio;
                            $tr['sumilla'] = $detfile[9];
                            $tr['email'] = $detfile[10];
                            $tr['telefono'] = $detfile[11];
                            $tr['usuario_created_at'] = Auth::user()->id;
                            $tr->save();

                            $flujo_interno = trim($detfile[13]);
                            $rf = array('');
                            if ($flujo_interno != '') {
                                $fi = FlujoInterno::where('flujo_id_interno', '=', ($flujo_interno * 1))
                                        ->where('estado', '=', '1')
                                        ->first();
                                if (count($fi) > 0) {
                                    if (trim($fi->flujo_id) != '') {
                                        $rf = RutaFlujo::where('flujo_id', '=', $fi->flujo_id)
                                                ->where('estado', '=', '1')
                                                ->first();
                                    } elseif (trim($fi->nombre) != '') {
                                        $sql = "  SELECT rf.*
                                                        FROM rutas_flujo rf
                                                        INNER JOIN flujos f ON rf.flujo_id=f.id AND f.estado=1
                                                        WHERE f.nombre LIKE '" . $fi->nombre . "%'
                                                        AND FIND_IN_SET(SUBSTRING_INDEX(f.nombre,' ',-1),(SELECT nemonico FROM areas WHERE id=" . $ainterna->area_id . "))>0";
                                        $qsql = DB::select($sql);
                                        $rf = $qsql[0];
                                    }
                                } else {
                                    $rf = RutaFlujo::where('flujo_id', '=', $ainterna->flujo_id)
                                            ->where('estado', '=', '1')
                                            ->first();
                                }
                            } else {
                                $rf = RutaFlujo::where('flujo_id', '=', $ainterna->flujo_id)
                                        ->where('estado', '=', '1')
                                        ->first();
                            }

                            $rutaFlujo = RutaFlujo::find($rf->id);


                            $ruta = new Ruta;
                            $ruta['tabla_relacion_id'] = $tr->id;
                            $ruta['fecha_inicio'] = $fecha_inicio;
                            $ruta['ruta_flujo_id'] = $rutaFlujo->id;
                            $ruta['flujo_id'] = $rutaFlujo->flujo_id;
                            $ruta['persona_id'] = $rutaFlujo->persona_id;
                            $ruta['area_id'] = $rutaFlujo->area_id;
                            $ruta['usuario_created_at'] = Auth::user()->id;
                            $ruta->save();

                            /*                             * **********Agregado de referidos************ */
                            $referido = new Referido;
                            $referido['ruta_id'] = $ruta->id;
                            $referido['tabla_relacion_id'] = $tr->id;
                            $referido['tipo'] = 0;
                            $referido['referido'] = $tr->id_union;
                            $referido['fecha_hora_referido'] = $tr->created_at;
                            $referido['usuario_referido'] = $tr->usuario_created_at;
                            $referido['usuario_created_at'] = Auth::user()->id;
                            $referido->save();
                            /*                             * ******************************************* */

                            $qrutaDetalle = DB::table('rutas_flujo_detalle')
                                    ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                    ->where('estado', '=', '1')
                                    ->orderBy('norden', 'ASC')
                                    ->get();
                            $validaactivar = 0;
                            foreach ($qrutaDetalle as $rd) {
                                $cero='';
                                if($rd->norden<10){
                                    $cero='0';
                                }
                                $rutaDetalle = new RutaDetalle;
                                $rutaDetalle['ruta_id'] = $ruta->id;
                                $rutaDetalle['area_id'] = $rd->area_id;
                                $rutaDetalle['tiempo_id'] = $rd->tiempo_id;
                                $rutaDetalle['dtiempo'] = $rd->dtiempo;
                                $rutaDetalle['norden'] =  $cero.$rd->norden;
                                $rutaDetalle['estado_ruta'] = $rd->estado_ruta;
                                if ($rd->norden == 1 or $rd->norden == 2 or ( $rd->norden > 1 and $validaactivar == 0 and $rd->estado_ruta == 2)) {
                                    if ($rd->norden == 1) {
                                        $rutaDetalle['dtiempo_final'] = $fecha_inicio;
                                        $rutaDetalle['tipo_respuesta_id'] = 2;
                                        $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                        $rutaDetalle['observacion'] = "";
                                        $rutaDetalle['usuario_updated_at'] = Auth::user()->id;
                                        $rutaDetalle['updated_at'] = $fecha_inicio;
                                    }
                                    $rutaDetalle['fecha_inicio'] = $fecha_inicio;
                                } else {
                                    $validaactivar = 1;
                                }
                                $rutaDetalle['usuario_created_at'] = Auth::user()->id;
                                $rutaDetalle->save();

                                $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                                        ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                        ->where('estado', '=', '1')
                                        ->orderBy('orden', 'ASC')
                                        ->get();
                                if (count($qrutaDetalleVerbo) > 0) {
                                    foreach ($qrutaDetalleVerbo as $rdv) {
                                        $rutaDetalleVerbo = new RutaDetalleVerbo;
                                        $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                                        $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                                        $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                                        $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                                        $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                                        $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                                        $rutaDetalleVerbo['orden'] = $rdv->orden;
                                        $rutaDetalleVerbo['usuario_created_at'] = Auth::user()->id;

                                        if ($rd->norden == 1) {
                                            $rutaDetalleVerbo['usuario_updated_at'] = Auth::user()->id;
                                            $rutaDetalleVerbo['updated_at'] = $fecha_inicio;
                                            $rutaDetalleVerbo['finalizo'] = 1;
                                        }

                                        $rutaDetalleVerbo->save();
                                    }
                                }
                            }
                            DB::commit();
                        }
                    } //es codigo nuevo
                }// valida si tiene flujo id
                //}// Apartir del 2 registro
            }// for del file

            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                'data' => $array,
                                'existe' => $arrayExist
                            )
            );
        }
    }

    public function postCargarequerimiento() {
        ini_set('memory_limit', '512M');
        ini_set('post_max_size', '64M');
        ini_set('upload_max_filesize', '64M');
        ini_set('max_execution_time',300);
        
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/requerimiento';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }

            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            $file = file('txt/requerimiento/' . $archivoNuevo);
            //$file=file('/var/www/html/ingind/public/txt/requerimiento/'.$archivoNuevo);
            $usuario_id = 1272;
            $auxArea = '';
            $auxId = '';
            $auxRutaId='';
            $auxRutaFecha='';
            $area_id='';
            for ($i = 0; $i < count($file); $i++) {
 
                DB::beginTransaction();
                if (trim($file[$i]) != '') {
                    $detfile = explode("\t", $file[$i]);

                    for ($j = 0; $j < count($detfile); $j++) {
                        $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                        $reemplazar = "";
                        $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                        $array[$i][$j] = $detfile[$j];
                    }
                    $vartipo = 0; //SERVICIO 
                    if (strpos($detfile[3], 'SERVICIO') == false) {
                        $vartipo = 1; // BIEN     
                    }
                    // Dar formato a  fechas
                    $fecha = explode('/', $detfile[8]);
                    $nuevaFecha = $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0] . ' 15:00:00';
                    
                 // Encontrar área en procesos
                $area_id = $this->BuscarArea(utf8_encode($detfile[2]));
                $area = Area::find($area_id);

                if (!$area) {
                    $nemonico = 'XX';
                } else {
                    $nemonico = $area->nemonico_doc;
                }
                
                    $tablaRelacion=DB::table('tablas_relacion as tr')
                            ->join(
                                'rutas as r',
                                'tr.id','=','r.tabla_relacion_id'
                            )
                            ->where('tr.id_union', '=', 'REQUERIMIENTO - N° ' . str_pad($detfile[1], 6, '0', STR_PAD_LEFT) . ' - ' . $detfile[0] . ' - ' . $nemonico)
                            ->where('tr.estado', '=', '1')
                            ->where('r.estado', '=', '1')
                            ->get();



                        if (count($tablaRelacion)==0 and ($detfile[1] != $auxId OR utf8_encode($detfile[2]) != $auxArea) and $detfile[4] != '0') {
                            if($auxRutaId!=''){
                               $rdD= RutaDetalle::where('ruta_id','=',$auxRutaId)
                                                ->where('dtiempo_final','!=','')
                                                ->select(DB::raw('MAX(CONCAT_WS("|",norden,id)) as id'))->first() ; 
                               $idrdv=explode('|',$rdD->id);
                               $idd=$idrdv[1]+1;
                               $rd= RutaDetalle::find($idd);
                               $rd['dtiempo']=15;
                               $rd['fecha_inicio']='2017-08-10 15:00:00';
                               $rd->save();
                            }
                            $auxId = $detfile[1];
                            $auxArea = utf8_encode($detfile[2]);
                            $auxRutaId='';
                            $auxRutaFecha='';
                            // Encontrar área en procesos
                            $area_id = $this->BuscarArea(utf8_encode($detfile[2]));
                            $area = Area::find($area_id);

                            if (!$area) {
                                $nemonico = 'XX';
                            } else {
                                $nemonico = $area->nemonico_doc;
                            }



                            $tablarelacion = new TablaRelacion;
                            $tablarelacion->software_id = 1;
                            $tablarelacion->id_union = 'REQUERIMIENTO - N° ' . str_pad($detfile[1], 6, '0', STR_PAD_LEFT) . ' - ' . $detfile[0] . ' - ' . $nemonico;
                            $tablarelacion->sumilla = $detfile[3];
                            $tablarelacion->estado = 1;
                            $tablarelacion->fecha_tramite = $nuevaFecha;
                            $tablarelacion->usuario_created_at = Auth::user()->id;

                            $tablarelacion->save();

                            /*                         * ************ ENCONTRAR RUTA DE ÁREA *************** */


                            $area_id = $this->BuscarArea(utf8_encode($detfile[2]));
                            $Ssql = '';
                            $Ssql .= "SELECT rf.id
                                        FROM rutas_flujo rf
                                        INNER JOIN rutas_flujo_detalle rfd ON  rfd.ruta_flujo_id=rf.id
                                        INNER  JOIN flujos f ON  f.id=rf.flujo_id
                                        WHERE rfd.norden=1 AND rfd.area_id=" . $area_id .
                                    " AND f.nombre LIKE '%REQUERIMIENTO%' 
                                        AND f.nombre LIKE '%DIRECTO%'";

                            if ($vartipo == 1) {
                                $Ssql .= "AND  f.nombre LIKE '%BIENES%' ";
                            } else {
                                $Ssql .= "AND  f.nombre LIKE '%SERVICIO%' ";
                            }
                            $rutaflujo_id = DB::select($Ssql);
                            $rutaFlujo = RutaFlujo::find($rutaflujo_id[0]->id);

                            $ruta = new Ruta;
                            $ruta['tabla_relacion_id'] = $tablarelacion->id;
                            $ruta['fecha_inicio'] = $nuevaFecha;
                            $ruta['ruta_flujo_id'] = $rutaFlujo->id;
                            $ruta['flujo_id'] = $rutaFlujo->flujo_id;
                            $ruta['persona_id'] = $rutaFlujo->persona_id;
                            $ruta['area_id'] = $rutaFlujo->area_id;
                            $ruta['usuario_created_at'] = Auth::user()->id;
                            $ruta->save();
                            $auxRutaId=$ruta->id;
                            $auxRutaFecha=$ruta->fecha_inicio;

     /*                             * **********Agregado de referidos************ */
                                $referido = new Referido;
                                $referido['ruta_id'] = $ruta->id;
                                $referido['tabla_relacion_id'] = $tablarelacion->id;
                                $referido['tipo'] = 0;
                                $referido['referido'] = $tablarelacion->id_union;
                                $referido['fecha_hora_referido'] = $tablarelacion->created_at;
                                $referido['usuario_referido'] = $tablarelacion->usuario_created_at;
                                $referido['usuario_created_at'] = $usuario_id;
                                $referido->save();

                            $qrutaDetalle = DB::table('rutas_flujo_detalle')
                                    ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                    ->where('estado', '=', '1')
                                    ->orderBy('norden', 'ASC')
                                    ->get();

                            foreach ($qrutaDetalle as $rd) {
                                $rutaDetalle = new RutaDetalle;
                                $rutaDetalle['ruta_id'] = $ruta->id;
                                $rutaDetalle['area_id'] = $rd->area_id;
                                $rutaDetalle['tiempo_id'] = $rd->tiempo_id;
                                $rutaDetalle['dtiempo'] = $rd->dtiempo;
                                $rutaDetalle['norden'] = $rd->norden;
                                $rutaDetalle['estado_ruta'] = $rd->estado_ruta;

                                $rutaDetalle['usuario_created_at'] = Auth::user()->id;
                                $rutaDetalle->save();

                                if ($rutaDetalle->norden == 1) {
                                    $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                    $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                    $rutaDetalle['tipo_respuesta_id'] = 1;
                                    $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                    $rutaDetalle['observacion'] = '';
                                    $rutaDetalle->save();
                                }

                                $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                                        ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                        ->where('estado', '=', '1')
                                        ->orderBy('orden', 'ASC')
                                        ->get();
                                if (count($qrutaDetalleVerbo) > 0) {
                                    foreach ($qrutaDetalleVerbo as $rdv) {
                                        $rutaDetalleVerbo = new RutaDetalleVerbo;
                                        $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                                        $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                                        $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                                        $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                                        $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                                        $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                                        $rutaDetalleVerbo['orden'] = $rdv->orden;
                                        $rutaDetalleVerbo['usuario_created_at'] = Auth::user()->id;
                                        $rutaDetalleVerbo->save();
                                    }
                                }
                                if($rutaDetalle->norden == 1){

                                $rutaDetalleVerbo = RutaDetalleVerbo::where('ruta_detalle_id', '=', $rutaDetalle->id)
                                                ->where('estado', '=', 1)->get();
                                foreach ($rutaDetalleVerbo as $r) {
                                    $rdv = RutaDetalleVerbo::find($r->id);
                                    if ($rdv->verbo_id == 1 and utf8_encode($detfile[7])!='') {
                                        $rdv['documento'] = utf8_encode($detfile[7]);
                                    }
                                    $rdv['finalizo'] = 1;
                                    $rdv['observacion'] = 'AUTOMATICO';
                                    $rdv['usuario_created_at'] = 1272;
                                    $rdv['usuario_updated_at'] = 1272;
                                    $rdv['updated_at'] = $nuevaFecha;
                                    $rdv->save();
                                }
                                }
                            }
                            
                            if($detfile[4]!=1){
                             $i--;
                            }
                        } else {
    //                        $fecha = explode('/', $detfile[9]);
    //                        $nuevaFecha = $fecha[2] . '-' . $fecha[1] . '-' . $fecha[0] . ' 08:00:00';
    
                            $rutaDetalle = array();
                            $auxRutaFecha=$nuevaFecha;
                            if ($detfile[4]*1 == 2) {
                                $varposicion=2;
                                if($area_id==26){
                                    $varposicion = 1;
                                }

                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '3') {
                                $varposicion=3;
                                if($area_id==26){
                                    $varposicion = 2;
                                }
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '4') {
                                $varposicion=4;
                                if ($area_id == 26) {
                                    $varposicion=3;
                                }
                                if (utf8_encode($detfile[5]) == 'Gerencia de Planificación, Presupuesto y Racionalización') {

                                    $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                    ->where('ruta_id', '=', $ruta->id)->first();
                                    $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                    $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                    $rutaDetalle['tipo_respuesta_id'] = 1;
                                    $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                    $rutaDetalle['observacion'] = '';
                                    $rutaDetalle->save();
                                }else {

                                        $Ssql="SELECT MAX(id) as id "
                                                . "FROM referidos "
                                                . "WHERE ruta_id=".$ruta->id;
                                        $refe=DB::select($Ssql);

                                        $referido = Referido::find($refe[0]->id);
                                        $referido['referido'] = utf8_encode($detfile[7]).'|'.$referido->referido;
                                        $referido->save();


                                }
                            }

                            if ($detfile[4] == '5') {
                                $varposicion=4;
                                if ($area_id == 26) {
                                    $varposicion=3;
                                }
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '6' or $detfile[4] == '8') {

                                $varposicion = 7;
                                if($area_id==26){
                                    $varposicion = 6;
                                }
                                if($area_id==29){
                                    $varposicion = 5;
                                }
                                if ($vartipo == 1) {
                                     if($area_id==29 or $area_id==38 or $area_id==26){
                                        $varposicion = 7;
                                     }else{
                                        $varposicion = 8;
                                     }
                                }
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if ($detfile[4] == '7') {

                                $varposicion = 8;
                                if($area_id==26){
                                    $varposicion = 7;
                                }
                                if($area_id==29){
                                    $varposicion = 6;
                                }
                                if ($vartipo == 1) {
                                      if($area_id==29 or $area_id==38 or $area_id==26){
                                          $varposicion = 8;
                                      }else{
                                          $varposicion = 9;

                                      }
                                }
                                $rutaDetalle = RutaDetalle::where('norden', '=', $varposicion)
                                                ->where('ruta_id', '=', $ruta->id)->first();
                                $rutaDetalle['fecha_inicio'] = $nuevaFecha;
                                $rutaDetalle['dtiempo_final'] = $nuevaFecha;
                                $rutaDetalle['tipo_respuesta_id'] = 1;
                                $rutaDetalle['tipo_respuesta_detalle_id'] = 1;
                                $rutaDetalle['observacion'] = '';
                                $rutaDetalle->save();
                            }

                            if (count($rutaDetalle) > 0) {
                                $rutaDetalleVerbo = RutaDetalleVerbo::where('ruta_detalle_id', '=', $rutaDetalle->id)
                                                ->where('estado', '=', 1)->get();
                                foreach ($rutaDetalleVerbo as $r) {
                                    $rdv = RutaDetalleVerbo::find($r->id);
                                    if ($rdv->verbo_id == 1 and utf8_encode($detfile[7])!='') {
                                        $rdv['documento'] = utf8_encode($detfile[7]);
                                         /** **********Agregado de referidos************ */
                                        $referido = new Referido;
                                        $referido['ruta_id'] = $ruta->id;
                                        $referido['tabla_relacion_id'] = $tablarelacion->id;
                                        $referido['tipo'] = 1;
                                        $referido['ruta_detalle_id'] = $rutaDetalle->id;
                                        $referido['norden'] = $rutaDetalle->norden;
                                        $referido['estado_ruta'] = $rutaDetalle->estado_ruta;
                                        $referido['referido'] = utf8_encode($detfile[7]);
                                        $referido['ruta_detalle_verbo_id'] = $rdv->id;
                                        $referido['fecha_hora_referido'] = $nuevaFecha;
                                        $referido['usuario_referido'] = $usuario_id;
                                        $referido['usuario_created_at'] = $usuario_id;
                                        $referido->save();

                                    }
                                    $rdv['finalizo'] = 1;
                                    $rdv['observacion'] = 'AUTOMATICO';
                                    $rdv['usuario_created_at'] = $usuario_id;
                                    $rdv['usuario_updated_at'] = $usuario_id;
                                    $rdv['updated_at'] = $nuevaFecha;
                                    $rdv->save();
                                }
                            }
                        }

                    
//                    $proveedor = Proveedor::where('ruc','=', $ruc_proveeedor)->first();
                    // --
                    // Muestra ultimos QUERY ejecutados
                    //$log = DB::getQueryLog();
                    //var_dump($obj);
                }
                DB::commit();
            }// for del file
            //exit;
                if($auxRutaId){
                               $rdD= RutaDetalle::where('ruta_id','=',$auxRutaId)
                                                ->where('dtiempo_final','!=','')
                                ->select(DB::raw('MAX(CONCAT_WS("|",norden,id)) as id'))->first() ; 
               $idrdv=explode('|',$rdD->id);
               $idd=$idrdv[1]+1;
                               $rd= RutaDetalle::find($idd);
                               $rd['dtiempo']=15;
                               $rd['fecha_inicio']='2017-08-10 15:00:00';
                               $rd->save();
                }
               
                        
            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                //'data'      => $array,
                                'data' => array(),
                                'existe' => 0//$arrayExist
                            )
            );
        }
    }

    // (RA - 2017/07/07): Carga de Archivo para los Gastos Contables.
    public function postCargargastos() { //Importante el nombre del metodo debe sser igual al de la función AJAX.
        ini_set('memory_limit', '512M');
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/contabilidad';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }

            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            $file=file('txt/contabilidad/'.$archivoNuevo);
            //$file = file('/var/www/html/ingind/public/txt/contabilidad/' . $archivoNuevo);

            for ($i = 0; $i < count($file); $i++) {

                DB::beginTransaction();
                if (trim($file[$i]) != '') {
                    $detfile = explode("\t", $file[$i]);

                    for ($j = 0; $j < count($detfile); $j++) {
                        $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                        $reemplazar = "";
                        $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                        $array[$i][$j] = $detfile[$j];
                    }

                    // Validar si existe dato
                    if (($detfile[8] * 1) > 0)
                        $ruc_proveeedor = $detfile[8];
                    else {
                        $bus_prov = Proveedor::where('id', '=', 1)->first(); // busca por default el RUC de la Municipalidad
                        $ruc_proveeedor = $bus_prov->ruc;
                    }

                    $proveedor = Proveedor::where('ruc', '=', $ruc_proveeedor)->first();

                    if (count($proveedor) == 0) {
                        $proveedor = new Proveedor;
                        $proveedor->ruc = $detfile[8];
                        $proveedor->proveedor = $detfile[9];
                        $proveedor->estado = 1;
                        $proveedor->usuario_created_at = Auth::user()->id;
                        $proveedor->save();
                    }


                    // Valida la Fecha
                    $arr_fecha_p = explode("/", $detfile[5]); //dd/mm/yyyy
                    if(@$arr_fecha_p[1])
                        $fecha_doc_p = $arr_fecha_p[2].'-'.$arr_fecha_p[1].'-'.$arr_fecha_p[0]; //yyy-mm-dd
                    else
                        $fecha_doc_p = $detfile[5];
                    // --

                    // Inserta Tabla contabilidad_gastos
                    $conta_gastos = GastosContables::where('contabilidad_proveedores_id', '=', $proveedor->id)
                                                    ->where('nro_expede', '=', $detfile[0])
                                                    ->where('anio_expede', '=', substr($fecha_doc_p, 0, 4))
                                                    ->first();
                    if (count($conta_gastos) == 0) {
                        // Usar este ejemplo para insertar datos ya que mantiene el ultimo valor ingresado.
                        $conta_gastos = new GastosContables;
                        $conta_gastos->contabilidad_proveedores_id = $proveedor->id;
                        $conta_gastos->nro_expede = $detfile[0];
                        $conta_gastos->anio_expede = substr($fecha_doc_p, 0, 4);
                        $conta_gastos->estado = 1;
                        $conta_gastos->usuario_created_at = Auth::user()->id;
                        $conta_gastos->save();
                    }
                    // --

                    if ($detfile[1] != '') {
                        if ($detfile[1] == 'GC') {
                            $monto_expede = $detfile[2];
                        } elseif ($detfile[1] == 'GD') {
                            $monto_expede = $detfile[3];
                        } else {
                            $monto_expede = $detfile[4];
                        }
                    }
                    
                    $conta_gastos_deta = GastosDetallesContables::where('contabilidad_gastos_id', '=', $conta_gastos->id)
                                                                ->where('tipo_expede', '=', $detfile[1])
                                                                ->where('monto_expede', '=', $monto_expede)
                                                                ->where('fecha_documento', '=', $detfile[5])
                                                                ->where('documento', '=', $detfile[6])
                                                                ->where('nro_documento', '=', $detfile[7])
                                                                ->where('esp_d', '=', $detfile[10])
                                                                //->where('fecha_doc_b', '=', $detfile[11])
                                                                //->whereNull('fecha_doc_b
                                                                ->where('doc_b', '=', $detfile[12])
                                                                ->where('nro_doc_b', '=', $detfile[13])
                                                                ->where('persona_doc_b', '=', $detfile[14])
                                                                ->where('consecutivo', '=', $detfile[16])
                                                                ->first();
                       
                    if (count($conta_gastos_deta) == 0)
                    {
                        // Valida la Fecha
                        $arr_fecha = explode("/", $detfile[5]); //dd/mm/yyyy
                        if(@$arr_fecha[1])
                            $fecha_doc = $arr_fecha[2].'-'.$arr_fecha[1].'-'.$arr_fecha[0]; //yyy-mm-dd
                        else
                            $fecha_doc = $detfile[5];
                        // --

                        // Valida Moneda
                        $arr_monto = explode(",", $monto_expede); // 0,000.00
                        if(@$arr_monto[1])
                            $nv_monto_expede = implode("", $arr_monto).substr($monto_expede, -3); //0000.00
                        else
                            $nv_monto_expede = $monto_expede;
                        // --

                        $obj = new GastosDetallesContables();
                        $obj->contabilidad_gastos_id = $conta_gastos->id;
                        $obj->tipo_expede = $detfile[1];

                        if ($nv_monto_expede)
                            $obj->monto_expede = $nv_monto_expede;

                        if ($detfile[5] != '')
                            $obj->fecha_documento = $fecha_doc;

                        if ($detfile[6] != '')
                            $obj->documento = $detfile[6];

                        if ($detfile[7] != '')
                            $obj->nro_documento = $detfile[7];

                        if ($detfile[10] != '')
                            $obj->esp_d = $detfile[10];

                        if ($detfile[11] != '')
                            $obj->fecha_doc_b = $detfile[11];

                        if ($detfile[12] != '')
                            $obj->doc_b = $detfile[12];

                        if ($detfile[13] != '')
                            $obj->nro_doc_b = $detfile[13];

                        if ($detfile[14] != '')
                            $obj->persona_doc_b = $detfile[14];

                        if ($detfile[15] != '')
                            $obj->observacion = $detfile[15];

                        if ($detfile[16] != '')
                            $obj->consecutivo = $detfile[16];

                        $obj->estado = 1;
                        $obj->usuario_created_at = Auth::user()->id;
                        $obj->save();
                    }
                    // Muestra ultimos QUERY ejecutados
                    //$log = DB::getQueryLog();
                    //var_dump($obj);
                }
                DB::commit();
            }// for del file
            //exit;
            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                //'data'      => $array,
                                'data' => array(),
                                'existe' => 0//$arrayExist
                            )
            );
        }
    }
    
                    
    // (RA - 2018/06/25): Carga de Archivo para los Gastos Contables.
    public function postCargaractividades() {
        ini_set('memory_limit', '512M');
        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            $uploadFolder = 'txt/actividades';

            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder);
            }

            $nombreArchivo = explode(".", $_FILES['carga']['name']);
            $tmpArchivo = $_FILES['carga']['tmp_name'];
            $archivoNuevo = $nombreArchivo[0] . "_u" . Auth::user()->id . "_" . date("Ymd_his") . "." . $nombreArchivo[1];
            $file = $uploadFolder . '/' . $archivoNuevo;

            //@unlink($file);

            $m = "Ocurrio un error al subir el archivo. No pudo guardarse.";
            if (!move_uploaded_file($tmpArchivo, $file)) {
                return Response::json(
                                array(
                                    'upload' => FALSE,
                                    'rst' => '2',
                                    'msj' => $m,
                                    'error' => $_FILES['archivo'],
                                )
                );
            }

            $array = array();
            $arrayExist = array();

            //$file=file('txt/actividades/'.$archivoNuevo);
            $file = file('/var/www/html/ingind/public/txt/actividades/' . $archivoNuevo);

            for ($i = 0; $i < count($file); $i++) {

                DB::beginTransaction();
                if (trim($file[$i]) != '') {
                    $detfile = explode("\t", $file[$i]);

                    for ($j = 0; $j < count($detfile); $j++) {
                        $buscar = array(chr(13) . chr(10), "\r\n", "\n", "�", "\r", "\n\n", "\xEF", "\xBB", "\xBF");
                        $reemplazar = "";
                        $detfile[$j] = trim(str_replace($buscar, $reemplazar, $detfile[$j]));
                        $array[$i][$j] = $detfile[$j];
                    }

                    // Valida si existe dato
                    $persona = Persona::where('dni', '=', $detfile[0])->first();

                    if (count($persona) > 0)
                    {
                        //$fecha = date('Y-m-d');
                        $fecha_final = strtotime ( '+1 day' , strtotime ( $detfile[3] ) ) ;
                        $fecha_final = date ( 'Y-m-d' , $fecha_final );

                        $acti_personal = new ActividadPersonal();
                        $acti_personal->actividad = $detfile[1];
                        $acti_personal->fecha_inicio = $detfile[3].' '.'08:00:00';
                        $acti_personal->dtiempo_final = $fecha_final.' '.'08:00:00';

                        $acti_personal->cantidad = 0;                   // Por Default
                        $acti_personal->tipo = 2;                       // Por Default
                        $acti_personal->actividad_categoria_id = 217;   // 217 = ATENCIÓN DE DOCUMENTOS ADMINISTRATIVOS (FISCALIZACION)

                        $acti_personal->persona_id = $persona->id;
                        $acti_personal->area_id = $persona->area_id;
                        $acti_personal->usuario_created_at = Auth::user()->id;
                        $acti_personal->save();

                        // DS-0029054-2017
                        // INFORME - N° 000002 - 2018 - SGLP-GGA-MDI
                        $cantidad = preg_match_all('/(^|)((DOC(\.|)\ SIMP(LE|))|([d]([\ -]|)[s])|([a]([\ -]|)[n])|([e]([\ -]|)[x]([p]|)([\.]|)))(-|\ |)([0-9]{4,10})([-|\ ][0-9]{4}|)(,|\.|$|)/i', $detfile[2]);

                        if($cantidad <= 0)
                        {
                            // Obtener el ID del DOCUMENTO
                            $documento = explode('-', trim($detfile[2]));
                            $doc_nombre = trim($documento[0]);

                            preg_match('/^(\N|)(\ |)/i', trim($documento[1]), $matches, PREG_OFFSET_CAPTURE); // N° 000002
                            if($matches <= 0)
                                $doc_numero = trim($documento[1]);
                            else
                            {
                                $arr_bus = array(utf8_decode('N° '), utf8_decode('Nº '));
                                $doc_numero = trim(str_replace($arr_bus, '', $documento[1]));
                            }
                            
                            $doc_anio = trim($documento[2]);
                            
                            $doc_area = '';
                            if(@$documento[3] != '')
                                $doc_area = trim($documento[3]);
                            if(@$documento[4] != '')
                                $doc_area = $doc_area.'-'.$documento[4];
                            if(@$documento[5] != '')
                                $doc_area = $doc_area.'-'.$documento[5];
                            if(@$documento[6] != '')
                                $doc_area = $doc_area.'-'.$documento[6];
                            if(@$documento[7] != '')
                                $doc_area = $doc_area.'-'.$documento[7];
                            if(@$documento[8] != '')
                                $doc_area = $doc_area.'-'.$documento[8];
                                                        
                            $selectdd = "SELECT dd.id
                                            FROM doc_digital_temporal dd
                                                WHERE dd.titulo LIKE '%".$doc_nombre."%'
                                                AND dd.titulo LIKE '%".$doc_numero."%'
                                                AND dd.titulo LIKE '%".$doc_anio." - ".$doc_area."%'
                                                AND dd.estado = 1";
                            $documento_digital = DB::select($selectdd);

                            $acti_personal_archivo = new ActividadPersonalDocdigital();
                            $acti_personal_archivo->actividad_personal_id=$acti_personal->id;
                            $acti_personal_archivo->doc_digital_id=$documento_digital[0]->id;
                            $acti_personal_archivo->usuario_created_at = Auth::user()->id;
                            $acti_personal_archivo->save();

                            $sql=" UPDATE rutas r
                                    INNER JOIN rutas_detalle rd ON rd.ruta_id = r.id AND rd.condicion = 0 AND rd.estado = 1
                                    SET rd.persona_responsable_id=".$persona->id.", rd.usuario_updated_at=".Auth::user()->id.", rd.updated_at=now()
                                    WHERE r.estado = 1
                                        AND rd.fecha_inicio IS NOT NULL
                                        AND dtiempo_final IS NULL
                                        AND (
                                                    r.id IN (SELECT r.ruta_id
                                                            FROM referidos r
                                                            WHERE r.doc_digital_id = ".$acti_personal_archivo->doc_digital_id.")
                                                    OR
                                                    rd.id IN (  SELECT s.ruta_detalle_id
                                                                FROM sustentos s
                                                                WHERE s.doc_digital_id = ".$acti_personal_archivo->doc_digital_id.")
                                                )
                                        AND rd.area_id =".$persona->area_id;
                            DB::update($sql);
                            // ---
                        }
                        else
                        {
                            $acti_personal = ActividadPersonal::find($acti_personal->id);
                            $acti_personal->actividad = $detfile[1].' '.$detfile[2];
                            $acti_personal->save();
                        }                    
                        
                    // tipo_asignacion = 1 (PROCESO DE CATEGORIA)
                        $categoria= ActividadCategoria::find($acti_personal->actividad_categoria_id); 
                        /*
                        if(!$categoria->ruta_flujo_id)
                        {
                            // Registrar Flujo
                            $flujo=new Flujo;
                            $flujo->area_id=Auth::user()->area_id;
                            $flujo->categoria_id=17;
                            $flujo->nombre=$categoria->nombre;
                            $flujo->tipo_flujo=2;
                            $flujo->usuario_created_at=Auth::user()->id;
                            $flujo->save();

                            // Registrar Flujo Respuesta
                            $ftr=new FlujoTipoRespuesta;
                            $ftr->flujo_id=$flujo->id;
                            $ftr->tipo_respuesta_id=2;
                            $ftr->tiempo_id=1;
                            $ftr->dtiempo=0;
                            $ftr->usuario_created_at=Auth::user()->id;
                            $ftr->save();

                            // Registrar Ruta Flujo
                            if($flujo->id){
                                $rutaflujo = new RutaFlujo;
                                $rutaflujo->flujo_id = $flujo->id;
                                $rutaflujo->persona_id = Auth::user()->id;
                                $rutaflujo->area_id = Auth::user()->area_id;
                                $rutaflujo->usuario_created_at = Auth::user()->id;
                                $rutaflujo->save();
                            }
                            
                            $dias=date("Y-m-d", strtotime($value['ffin'])) - date("Y-m-d", strtotime($value['finicio']));                            
                            // Registrar Detalle de Ruta
                            if($rutaflujo->id) {
                                $rutaflujodetalle = new RutaFlujoDetalle;
                                $rutaflujodetalle->ruta_flujo_id = $rutaflujo->id;
                                $rutaflujodetalle->area_id = Auth::user()->area_id;
                                $rutaflujodetalle->tiempo_id = 2;
                                $rutaflujodetalle->dtiempo = $dias+1;
                                $rutaflujodetalle->norden = 1;
                                $rutaflujodetalle->detalle = "Desarrollo del trabajo";
                                $rutaflujodetalle->estado_ruta = 1;
                                $rutaflujodetalle->usuario_created_at = Auth::user()->id;
                                $rutaflujodetalle->save();
                            }

                            // Registrar Verbos de la ruta detalle
                            if($rutaflujodetalle->id) {
                                $rutaflujodetalleverbo=new RutaFlujoDetalleVerbo;
                                $rutaflujodetalleverbo->ruta_flujo_detalle_id = $rutaflujodetalle->id;
                                $rutaflujodetalleverbo->nombre = '';
                                $rutaflujodetalleverbo->condicion = 0;
                                $rutaflujodetalleverbo->rol_id = 4;
                                $rutaflujodetalleverbo->verbo_id = 3;
                                $rutaflujodetalleverbo->orden = 1;
                                $rutaflujodetalleverbo->nombre = 'Inicio de actividad';
                                $rutaflujodetalleverbo->usuario_created_at = Auth::user()->id;
                                $rutaflujodetalleverbo->save();

                                $rutaflujodetalleverbo=new RutaFlujoDetalleVerbo;
                                $rutaflujodetalleverbo->ruta_flujo_detalle_id = $rutaflujodetalle->id;
                                $rutaflujodetalleverbo->nombre = '';
                                $rutaflujodetalleverbo->condicion = 0;
                                $rutaflujodetalleverbo->rol_id = 4;
                                $rutaflujodetalleverbo->verbo_id = 3;
                                $rutaflujodetalleverbo->orden = 2;
                                $rutaflujodetalleverbo->nombre = 'Fin de actividad';
                                $rutaflujodetalleverbo->usuario_created_at = Auth::user()->id;
                                $rutaflujodetalleverbo->save();
                            }

                            // Actualizar ruta_flujo_id a la categoria
                            $categoria->ruta_flujo_id=$rutaflujo->id;
                            $categoria->save();
                        }
                        */                        
                        $rutaFlujo = RutaFlujo::find($categoria->ruta_flujo_id);

                        // ENCONTRAR CORRELATIVO EN ACTIVIDADES POR DIA
                        $result=Ruta::getCorrelativoAct($persona->id);

                        $tablarelacion = new TablaRelacion;
                        $tablarelacion->software_id = 1;
                        $tablarelacion->id_union = 'ACT - N° ' . str_pad($result+1, 2, '0', STR_PAD_LEFT) . ' - ' . $persona->dni. ' - '. Auth::user()->areas->nemonico_doc;
                        $tablarelacion->sumilla = $detfile[1];
                        $tablarelacion->estado = 1;
                        $tablarelacion->fecha_tramite =date('Y-m-d H:i:s');
                        $tablarelacion->usuario_created_at = Auth::user()->id;
                        $tablarelacion->save();
                        
                        // ENCONTRAR RUTA
                        $ruta = new Ruta;
                        $ruta['tabla_relacion_id'] = $tablarelacion->id;
                        $ruta['fecha_inicio'] = date('Y-m-d H:i:s');
                        $ruta['ruta_flujo_id'] = $rutaFlujo->id;
                        $ruta['flujo_id'] = $rutaFlujo->flujo_id;
                        $ruta['persona_id'] = $rutaFlujo->persona_id;
                        $ruta['area_id'] = $rutaFlujo->area_id;
                        $ruta['usuario_created_at'] = Auth::user()->id;
                        $ruta->save();

                        $qrutaDetalle = DB::table('rutas_flujo_detalle')
                                            ->where('ruta_flujo_id', '=', $rutaFlujo->id)
                                            ->where('estado', '=', '1')
                                            ->orderBy('norden', 'ASC')
                                            ->get();
                   
                        foreach ($qrutaDetalle as $rd)
                        {
                            $cero='';
                            if($rd->norden<10){
                                $cero='0';
                            }
                            $rutaDetalle = new RutaDetalle;
                            $rutaDetalle['ruta_id'] = $ruta->id;
                            $rutaDetalle['area_id'] = $rd->area_id;
                            $rutaDetalle['tiempo_id'] = $rd->tiempo_id;
                            // Calcula fecha Final
                            $sql="SELECT CalcularFechaFinal( '".date('Y-m-d H:i:s')."', (3*1440), ".$rd->area_id." ) fproy";
                            $fproy= DB::select($sql);                                
                            $rutaDetalle['fecha_proyectada'] = $fproy[0]->fproy;

                            $rutaDetalle['dtiempo'] = $rd->dtiempo;
                            $rutaDetalle['detalle'] = $rd->detalle;
                            $rutaDetalle['norden'] =$cero.$rd->norden;
                            $rutaDetalle['estado_ruta'] = $rd->estado_ruta;
                            $rutaDetalle['usuario_created_at'] = Auth::user()->id;

                            if ($rutaDetalle->norden == 1) {
                                $rutaDetalle['fecha_inicio'] = date('Y-m-d H:i:s');
                            }
                            $rutaDetalle->save();
                                                        
                            $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                                    ->where('ruta_flujo_detalle_id', '=', $rd->id)
                                    ->where('estado', '=', '1')
                                    ->orderBy('orden', 'ASC')
                                    ->get();
                            
                            if (count($qrutaDetalleVerbo) > 0) {
                                foreach ($qrutaDetalleVerbo as $rdv) {
                                    $rutaDetalleVerbo = new RutaDetalleVerbo;
                                    $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                                    $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                                    $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                                    $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                                    $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                                    $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                                    $rutaDetalleVerbo['orden'] = $rdv->orden;
                                    $rutaDetalleVerbo['usuario_created_at'] = Auth::user()->id;
                                if($categoria->tipo==1){
                                    $rutaDetalleVerbo['usuario_updated_at'] = $persona->id;
                                }
                                    $rutaDetalleVerbo->save();
                                }
                            }
                        }

                        //if($cantidad <= 0) {
                            // RUTA_ID en Actividad
                            $acti_personal->ruta_id=$ruta->id;
                            $acti_personal->ruta_detalle_id=$rutaDetalle->id;
                            $acti_personal->save();
                        //}
                    // --
                    }
                    
                }
                DB::commit();
            }
            
            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                'file' => $archivoNuevo,
                                'upload' => TRUE,
                                //'data'      => $array,
                                'data' => array(),
                                'existe' => 0 //$arrayExist
                            )
            );
        }
    }
    // --

    // (RA - 2018/07/11): Carga de Archivo para los Gastos Contables.
    public function postCargatributaria() {
        ini_set('memory_limit', '512M');

        if (isset($_FILES['carga']) and $_FILES['carga']['size'] > 0) {

            //$xlsx = new SimpleXLSX( 'D:\MUNICIPALIDAD\Trabajos Asignados\Ramesh - Carga de Excel para Tributarios\prueba.xlsx' );
            $xlsx = new SimpleXLSX( $_FILES['carga']['tmp_name'] );

            echo '<pre>';
            print_r($xlsx->rows());
            exit;            
            
            return Response::json(
                            array(
                                'rst' => '1',
                                'msj' => 'Archivo procesado correctamente',
                                //'file' => $archivoNuevo,
                                'upload' => TRUE,
                                'data' => array(),
                                'existe' => 0
                            )
            );
        }
    }



    public function BuscarArea($nombreArea) {

        if ($nombreArea == 'Alcadía')
            $area_id = 44;
        else if($nombreArea == 'Gerencia de Administración y Finanzas')
            $area_id = 26;
        else if($nombreArea == 'Gerencia de Asesoria Legal')
            $area_id = 27;
        else if($nombreArea == 'GERENCIA DE DESARROLLO ECONOMICO LOCAL')
            $area_id = 9;
        else if($nombreArea == 'Gerencia de Desarrollo Social')
            $area_id = 15;
        else if($nombreArea == 'Gerencia de Desarrollo Urbano')
            $area_id = 24;
        else if($nombreArea == 'Gerencia de Planificación, Presupuesto y Racionalización')
            $area_id = 28;
        else if($nombreArea == 'Sub Gerencia de Logística')
            $area_id = 29;
        else if($nombreArea == 'Gerencia de Fiscalizacion y Control Municipal')
            $area_id = 10;
        else if($nombreArea == 'Gerencia de Gestion Ambiental')
            $area_id = 21;
        else if($nombreArea == 'Gerencia de Infraestructura Pública')
            $area_id = 25;
        else if($nombreArea == 'GERENCIA DE MODERNIZACION DE LA GESTION MUNICIPAL')
            $area_id = 94;
        else if($nombreArea == 'Sub Gerencia de Tesorería')
            $area_id = 42;
        else if($nombreArea == 'SUB GERENCIA DE CONTABILIDAD Y COSTOS')
            $area_id = 35;
        else if($nombreArea == 'Gerencia de Promoción de la Inversión y Cooperación')
            $area_id = 12;
        else if($nombreArea == 'Gerencia de Rentas')
            $area_id = 11;
        else if($nombreArea == 'Gerencia de Secretaría General')
            $area_id = 30;
        else if($nombreArea == 'Gerencia de Seguimiento y Evaluación')
            $area_id = 31;
        else if($nombreArea == 'Gerencia de Seguridad Ciudadana')
            $area_id = 19;
        else if($nombreArea == 'Gerencia Municipal')
            $area_id = 32;
        else if($nombreArea == 'Organo de Control Institucional')
            $area_id = 33;
        else if($nombreArea == 'Procuraduria Pública Municipal')
            $area_id = 34;
        else if($nombreArea == 'Sub Gerencia de Areas Verdes y Saneamiento Ambiental')
            $area_id = 22;
        else if($nombreArea == 'Sub Gerencia de Ejecutoria Coactiva')
            $area_id = 36;
        else if($nombreArea == 'SUB GERENCIA DE IMAGEN INSTUTICIONAL Y PARTICIPACIÓN VECINAL')
            $area_id = 13;
        else if($nombreArea == 'Sub Gerencia de Juventudes, Recreacion y Deportes')
            $area_id = 17;
        else if($nombreArea == 'Sub Gerencia de la Mujer, Educación, Cultura, Serv. Social, OMAPED, CIAM, Y DEMUNA')
            $area_id = 16;
        else if($nombreArea == 'Sub Gerencia de la Tecnológia de Información y la Comunicación')
            $area_id = 14;
        else if($nombreArea == 'Sub Gerencia de Limpieza Publica')
            $area_id = 23;
        else if($nombreArea == 'Sub Gerencia de Personal')
            $area_id = 53;
        else if($nombreArea == 'Sub Gerencia de Programas Alimentarios Y Salud')
            $area_id = 18;
        else if($nombreArea == 'SUB GERENCIA DE SERVICIOS GENERALES')
            $area_id = 38;
        else if($nombreArea == 'Sub Gerencia de Vigilancia Ciudadana e Informacion')
            $area_id = 20;
        return $area_id;
    }
    
    public function postPoblarsubproceso(){
        $sql="SELECT rd.fecha_inicio,rd.dtiempo_final,rd.ruta_flujo_id rfid,r.id as ruta_id,id_union,MAX(rd.norden) as norden,tr.created_at
                ,GROUP_CONCAT(DISTINCT rdm.ruta_flujo_id) as ruta_flujo_id,GROUP_CONCAT(DISTINCT f.nombre) as flujo
                FROM tablas_relacion tr
                INNER JOIN rutas r ON r.tabla_relacion_id=tr.id and r.estado=1
                INNER JOIN rutas_detalle rd ON rd.ruta_id=r.id AND rd.estado=1  AND rd.dtiempo_final IS NULL

                INNER JOIN rutas_detalle_micro rdm ON rdm.ruta_id=rd.ruta_id
                INNER JOIN rutas_flujo rf ON rf.id=rdm.ruta_flujo_id
                INNER JOIN flujos f ON f.id=rf.flujo_id and f.nombre LIKE 'MP - REQUERIMIENTO SERVICIO DIRECTO LOCADORES%'
                WHERE tr.id_union like '%REQUERIMIENTO%'
                AND DATE(r.fecha_inicio) BETWEEN '2018-04-01' AND '2018-04-30'
                AND tr.estado=1
                AND YEAR(tr.created_at)='2018'
                -- AND r.id=318607 -- prueba
                GROUP BY tr.id
                HAVING norden ='03'";
        $result=DB::select($sql);
        
        foreach($result as $r){
//            var_dump($r);exit();
           DB::beginTransaction();
                
                $rd=RutaDetalle::where('norden','=',$r->norden)
                                ->where('ruta_id','=',$r->ruta_id)
                                ->where('estado','=',1)
                                ->whereNull('dtiempo_final')
                                ->first();
                $rd->ruta_flujo_id=$r->ruta_flujo_id;
                $rd->save();

                $rf= RutaFlujo::find($rd->ruta_flujo_id);

                $rutaflujodetalle = DB::table('rutas_flujo_detalle')
                        ->where('ruta_flujo_id', '=', $rf->id)
                        ->where('estado', '=', '1')
                        ->orderBy('norden', 'ASC')
                        ->get();
                foreach ($rutaflujodetalle as $rfd) {
                    $cero='';
                    if($rfd->norden<10){
                        $cero='0';
                    }
                    $rutaDetalle = new RutaDetalle;
                    $rutaDetalle['ruta_id'] = $rd->ruta_id;
                    $rutaDetalle['area_id'] = $rfd->area_id;
                    $rutaDetalle['tiempo_id'] = $rfd->tiempo_id;
                    $rutaDetalle['dtiempo'] = $rfd->dtiempo;
                    $rutaDetalle['ruta_flujo_id_dep']=$rd->ruta_flujo_id;
                    $rutaDetalle['detalle']=$rfd->detalle;
                    $rutaDetalle['archivado']=$rfd->archivado;
                    $rutaDetalle['norden'] = $rd->norden.'.'.$cero.$rfd->norden;
                    $rutaDetalle['estado_ruta'] = $rfd->estado_ruta;
                    $rutaDetalle['usuario_created_at'] = Auth::user()->id;
                    $rutaDetalle->save();

                    $qrutaDetalleVerbo = DB::table('rutas_flujo_detalle_verbo')
                            ->where('ruta_flujo_detalle_id', '=', $rfd->id)
                            ->where('estado', '=', '1')
                            ->orderBy('orden', 'ASC')
                            ->get();

                    if (count($qrutaDetalleVerbo) > 0) {
                        foreach ($qrutaDetalleVerbo as $rdv) {
                            $rutaDetalleVerbo = new RutaDetalleVerbo;
                            $rutaDetalleVerbo['ruta_detalle_id'] = $rutaDetalle->id;
                            $rutaDetalleVerbo['nombre'] = $rdv->nombre;
                            $rutaDetalleVerbo['condicion'] = $rdv->condicion;
                            $rutaDetalleVerbo['rol_id'] = $rdv->rol_id;
                            $rutaDetalleVerbo['verbo_id'] = $rdv->verbo_id;
                            $rutaDetalleVerbo['documento_id'] = $rdv->documento_id;
                            $rutaDetalleVerbo['orden'] = $rdv->orden;
                            $rutaDetalleVerbo['usuario_created_at'] = Auth::user()->id;
                            $rutaDetalleVerbo->save();
                        }
                    }
                }
                //2do nivel 
                $rutaflujodetallemicro= RutaFlujoDetalleMicro::where('ruta_flujo_id','=',$rf->id)
                                                ->where('estado','=',1)
                                                ->orderBy('norden','ASC')->get();

                foreach ($rutaflujodetallemicro as $rfdm) {
                    $cero='';
                    if($rfdm->norden<10){
                        $cero='0';
                    }
                    $rdmcreate= new RutaDetalleMicro;
                    $rdmcreate->ruta_flujo_id=$rfdm->ruta_flujo_id2;
                    $rdmcreate->ruta_id=$rd->ruta_id;
                    $rdmcreate->norden=$rd->norden.'.'.$cero.$rfdm->norden;
                    $rdmcreate->usuario_created_at=Auth::user()->id;       
                    $rdmcreate->save();
                }
                //--
            DB::commit();
        }
        
        return  array(
                    'rst'=>1,
                    'msj'=>'Registro realizado con éxito'
            );
    }

}
