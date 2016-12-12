<?php

class PlantillaDocumento extends Base {
	protected $fillable = [];
    public $table = "plantilla_doc";
    public static $where =['id', 'descripcion', 'tipo_documento_id', 'area_id', 'cuerpo', 'estado'];
    public static $selec =['id', 'descripcion', 'tipo_documento_id', 'area_id', 'cuerpo', 'estado'];

    public static function getPlantillas(){
        return DB::table('plantilla_doc as pd')
        		->join('documentos as d', 'pd.tipo_documento_id', '=', 'd.id')
        		->join('areas as a', 'pd.area_id', '=', 'a.id')
                ->select('pd.id', 'pd.descripcion','pd.descripcion as nombre','pd.cuerpo', 'd.nombre as tipodoc', 'a.nombre as area','pd.estado','pd.area_id','pd.tipo_documento_id','a.nemonico_doc')
               	->where( 
                    function($query){
                    	if ( Input::get('id') ) {
                            $query->where('pd.id','=',Input::get('id'));
                        }
                        if ( Input::get('area') ) {
                            /*$query->where('pd.area_id','=',Input::get('area'));*/
                            $sql = "SELECT a.id idarea FROM areas a 
                                    INNER JOIN area_cargo_persona acp ON acp.area_id=a.id AND acp.estado=1
                                    INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1 
                                    INNER JOIN personas p ON p.id=cp.persona_id AND p.estado = 1 AND p.id=".Auth::user()->id." AND p.rol_id IN (8,9) 
                                    WHERE a.estado=1";
                                    $areas= DB::select($sql);
                                    $areas_id = '';
                                    foreach($areas as $key => $value){
                                        $areas_id.= ($key == 0) ? $value->idarea : ','.$value->idarea; 
                                    }
                                    $query->whereRaw('FIND_IN_SET( pd.area_id,"'.$areas_id.'")>0 ');
                        }
/*                        $query->where('pd.estado','=','1');*/
                    }
                )
                ->orderBy('pd.id')
                ->get();
    }

}