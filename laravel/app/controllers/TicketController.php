<?php

class TicketController extends \BaseController
{
     /**
     * cargar tickets, mantenimiento
     * POST /area/cargar
     *
     * @return Response
     */

    public function postCargar()
    {
        if ( Request::ajax() ) {
            /*********************FIJO*****************************/
            $array=array();
            $array['where']='';$array['usuario']=Auth::user()->id;
            $array['limit']='';$array['order']='';
            
            if (Input::has('draw')) {
                if (Input::has('order')) {
                    $inorder=Input::get('order');
                    $incolumns=Input::get('columns');
                    $array['order']=  ' ORDER BY '.
                                      $incolumns[ $inorder[0]['column'] ]['name'].' '.
                                      $inorder[0]['dir'];
                }

                $array['limit']=' LIMIT '.Input::get('start').','.Input::get('length');
                $aParametro["draw"]=Input::get('draw');
            }
            /************************************************************/

            if( Input::has("persona_id") ){
                $persona_id=Input::get("persona_id");
                if( trim( $persona_id )!='' ){
                    $array['where'].=" AND t.persona_id LIKE '%".$persona_id."%' ";
                }
            }
             if( Input::has("area_id") ){
                $area_id=Input::get("area_id");
                if( trim( $area_id )!='' ){
                    $array['where'].=" AND t.area_id LIKE '%".$area_id."%' ";
                }
            }
            if( Input::has("descripcion") ){
                $descripcion=Input::get("descripcion");
                if( trim( $descripcion )!='' ){
                    $array['where'].=" AND t.descripcion LIKE '%".$descripcion."%' ";
                }
            }

            if( Input::has("fecha_pendiente") ){
                $fecha_pendiente=Input::get("fecha_pendiente");
                if( trim( $fecha_pendiente )!='' ){
                    $array['where'].=" AND t.fecha_pendiente LIKE '%".$fecha_pendiente."%' ";
                }
            }
            if( Input::has("fecha_atencion") ){
                $fecha_atencion=Input::get("fecha_atencion");
                if( trim( $fecha_atencion )!='' ){
                    $array['where'].=" AND t.fecha_atencion LIKE '%".$fecha_atencion."%' ";
                }
            }
            if( Input::has("fecha_solucion") ){
                $fecha_solucion=Input::get("fecha_solucion");
                if( trim( $fecha_solucion )!='' ){
                    $array['where'].=" AND t.fecha_solucion LIKE '%".$fecha_solucion."%' ";
                }
            }

            if( Input::has("estado") ){
                $estado=Input::get("estado");
                if( trim( $estado )!='' ){
                    $array['where'].=" AND t.estado='".$estado."' ";
                }
            }

            $array['order']=" ORDER BY t.persona_id ";

            $cant  = Ticket::getCargarCount( $array );
            $aData = Ticket::getCargar( $array );

            $aParametro['rst'] = 1;
            $aParametro["recordsTotal"]=$cant;
            $aParametro["recordsFiltered"]=$cant;
            $aParametro['data'] = $aData;
            $aParametro['msj'] = "No hay registros aún";   
            return Response::json($aParametro);

        }
    }
    /**
     * Store a newly created resource in storage.
     * POST /area/listar
     *
     * @return Response
     */
    public function postListar()
    {
        if ( Request::ajax() ) {
            $t      = new Ticket;
            $listar = Array();
            $listar = $t->getTicket();
         
            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $listar
                )
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     * POST /area/crear
     *
     * @return Response
     */
    public function postCrear()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
                //'persona_id' => $required.'|'.$regex,
            );
            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }

            $tickets = new Ticket;
            $tickets->persona_id = Input::get('solicitante');
            $tickets->area_id = Input::get('solicitante_area');
            $tickets->descripcion = Input::get('descripcion');
            $tickets->fecha_pendiente = Input::get('fecha_pendiente');
            $tickets->estado = Input::get('estado');
            $tickets->usuario_created_at = Auth::user()->id;
            $tickets->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
                'ticket_id'=>$tickets->id,

                )
            );
        }
    }

    /**
     * Update the specified resource in storage.
     * POST /area/editar
     *
     * @return Response
     */
    public function postEditar()
    {
        if ( Request::ajax() ) {
            $regex='regex:/^([a-zA-Z .,ñÑÁÉÍÓÚáéíóú]{2,60})$/i';
            $required='required';
            $reglas = array(
              //  'persona_id' => $required.'|'.$regex,
            );

            $mensaje= array(
                'required'    => ':attribute Es requerido',
                'regex'        => ':attribute Solo debe ser Texto',
            );

            $validator = Validator::make(Input::all(), $reglas, $mensaje);

            if ( $validator->fails() ) {
                return Response::json(
                    array(
                    'rst'=>2,
                    'msj'=>$validator->messages(),
                    )
                );
            }

            $ticketId = Input::get('id');
            $tickets = Ticket::find($ticketId);
            $tickets->persona_id = Input::get('persona_id');
            $tickets->area_id = Input::get('area_id');
            $tickets->descripcion = Input::get('descripcion');
            $tickets->estado = Input::get('estado');
            $tickets->usuario_updated_at = Auth::user()->id;
            $tickets->save();
           
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );
        }
    }

    /**
     * Changed the specified resource from storage.
     * POST /area/cambiarestado
     *
     * @return Response
     */
    public function postCambiarestado()
    {

        if ( Request::ajax() ) {
            $ticketId = Input::get('id');
            $ticket = Ticket::find($ticketId);
            $ticket->usuario_updated_at = Auth::user()->id;
            $ticket->estado = Input::get('estado');
            $ticket->save();
        
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }



}
