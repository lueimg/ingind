<?php

class AreaController extends \BaseController
{

    /**
     * cargar areas, mantenimiento
     * POST /area/cargar
     *
     * @return Response
     */
    public function postCargar()
    {
        //si la peticion es ajax
        if ( Request::ajax() ) {
            $areas = Area::get(Input::all());
            return Response::json(array('rst'=>1,'datos'=>$areas));
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
            $a      = new Area;
            $listar = Array();
            $listar = $a->getArea();

            return Response::json(
                array(
                    'rst'   => 1,
                    'datos' => $listar
                )
            );
        }
    }
    /**
     * 
     */
    public function postImagenp()
    {
        if (Input::hasFile('imagenp')) {
            if ( Input::file('imagenp')->isValid() ) {
                $areaId = Input::get('idp');
                $file = Input::file('imagenp');
                $tmpArchivo = $file->getRealPath();
                $extension = $file->getClientOriginalExtension();
                //$name = $file->getClientOriginalName();
                $name = 'a'.$areaId.'.'.$extension;
                $destinationPath='img/admin/area';
                if ($file->move($destinationPath,$name)){
                    $areas = Area::find($areaId);
                    $areas->imagen = $name;
                    $areas->save();
                    return Response::json(
                        array(
                            'rst'   => 1,
                            'datos' => 'Se subio con exito'
                        )
                    );
                } else {
                    return Response::json(
                        array(
                            'rst'   => 0,
                            'datos' => 'No se subio con exito'
                        )
                    );
                }
            }
        }
        return Response::json(
            array(
                'rst'   => 0,
                'datos' => 'No se subio con exito'
            )
        );
    }
    /**
     * 
     */
    public function postImagenc()
    {
        if (Input::hasFile('imagenc')) {
            if ( Input::file('imagenc')->isValid() ) {
                $areaId = Input::get('idc');
                $file = Input::file('imagenc');
                $tmpArchivo = $file->getRealPath();
                $extension = $file->getClientOriginalExtension();
                //$name = $file->getClientOriginalName();
                $name = 'a'.$areaId.'c.'.$extension;
                $destinationPath='img/admin/area';
                if ($file->move($destinationPath,$name)){
                    $areas = Area::find($areaId);
                    $areas->imagenc = $name;
                    $areas->save();
                    return Response::json(
                        array(
                            'rst'   => 1,
                            'datos' => 'Se subio con exito'
                        )
                    );
                } else {
                    return Response::json(
                        array(
                            'rst'   => 0,
                            'datos' => 'No se subio con exito'
                        )
                    );
                }
            }
        }
        return Response::json(
            array(
                'rst'   => 0,
                'datos' => 'No se subio con exito'
            )
        );
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
                'nombre' => $required.'|'.$regex,
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

            $areas = new Area;
            $areas->nombre = Input::get('nombre');
            $areas->id_int = Input::get('id_int');
            $areas->id_ext = Input::get('id_ext');
            $areas->estado = Input::get('estado');
            $areas->save();

            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro realizado correctamente',
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
                'nombre' => $required.'|'.$regex,
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

            $areaId = Input::get('id');
            $areas = Area::find($areaId);
            $areas->nombre = Input::get('nombre');
            $areas->id_int = Input::get('id_int');
            $areas->id_ext = Input::get('id_ext');
            $areas->estado = Input::get('estado');
            $areas->save();
            if (Input::get('estado') == 0) {
                DB::table('area_cargo_persona')
                    ->where('area_id','=',$areaId)
                    ->update(array('estado' => 0));
            }
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
            $areaId = Input::get('id');
            $area = Area::find($areaId);
            $area->estado = Input::get('estado');
            $area->save();
            if (Input::get('estado') == 0) {
                DB::table('area_cargo_persona')
                    ->where('area_id','=',$areaId)
                    ->update(array('estado' => 0));
            }
            return Response::json(
                array(
                'rst'=>1,
                'msj'=>'Registro actualizado correctamente',
                )
            );    

        }
    }

}
