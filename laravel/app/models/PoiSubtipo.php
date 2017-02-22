<?php

class PoiSubtipo extends Base
{
    public $table = "poi_subtipos";
    public static $where =['id', 'nombre','tipo_id','costo_actual','tamano','color', 'estado'];
    public static $selec =['id', 'nombre','tipo_id','costo_actual','tamano','color', 'estado'];
    
    public static function getCargarCount( $array )
    {
        $sSql=" SELECT  COUNT(ps.id) cant
                FROM poi_subtipos ps
                INNER JOIN poi_tipos pt on pt.id=ps.tipo_id 
                WHERE 1=1 ";
        $sSql.= $array['where'];
        $oData = DB::select($sSql);
        return $oData[0]->cant;
    }

    public static function getCargar( $array )
    {
        $sSql=" SELECT ps.id, ps.nombre,tipo_id,ps.costo_actual, ps.tamano, ps.color, ps.estado,pt.nombre as tipo
                FROM poi_subtipos ps
                INNER JOIN poi_tipos pt on pt.id=ps.tipo_id
                WHERE 1=1 ";
        $sSql.= $array['where'].
                $array['order'].
                $array['limit'];
        $oData = DB::select($sSql);
        return $oData;
    }


    public function getPoiSubtipo(){
        $poi_subtipo=DB::table('poi_subtipos')
                ->select('id','nombre','estado')
                ->where( 
                    function($query){
                        if ( Input::get('estado') ) {
                            $query->where('estado','=','1');
                        }
                    }
                )
                ->orderBy('nombre')
                ->get();
                
        return $poi_subtipo;
    }
}
