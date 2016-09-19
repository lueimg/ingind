<?php
class TablaRelacion extends Eloquent
{
    public $table="tablas_relacion";

    public static function getPlataforma()
    {
        $usuario=Auth::user()->id;
        $sql="  SELECT r.id,f.nombre proceso, tr.id_union tramite, rd.fecha_inicio f1,rd2.fecha_inicio, COUNT(tr2.id) cant
                FROM rutas r
                INNER JOIN tablas_relacion tr ON tr.id=r.tabla_relacion_id AND tr.estado=1
                INNER JOIN flujos f ON f.id=r.flujo_id AND f.estado=1
                INNER JOIN rutas_detalle rd ON rd.ruta_id=r.id AND rd.estado=1 AND rd.norden=1 AND rd.area_id=52
                INNER JOIN rutas_detalle rd2 ON rd2.ruta_id=r.id AND rd2.estado=1 AND rd2.norden=2
                LEFT JOIN tablas_relacion tr2 ON tr2.id_union=tr.id_union AND tr2.estado=1 AND tr2.id>tr.id
                WHERE r.estado=1
                AND FIND_IN_SET(rd2.area_id,
                    (SELECT GROUP_CONCAT(a.id)
                    FROM area_cargo_persona acp
                    INNER JOIN areas a ON a.id=acp.area_id AND a.estado=1
                    INNER JOIN cargo_persona cp ON cp.id=acp.cargo_persona_id AND cp.estado=1
                    WHERE acp.estado=1
                    AND cp.persona_id= ".$usuario.")
                    )>0
                GROUP BY tr.id_union
                HAVING cant=0
                ORDER BY rd2.fecha_inicio DESC ";

        $r=DB::select($sql);

        return $r;
    }

    public function getRelacion()
    {
        $tr=         DB::table('tablas_relacion AS tr')
                    ->join(
                        'softwares AS s',
                        's.id', '=', 'tr.software_id'
                    )
                    ->select(
                        's.nombre AS software','tr.id_union AS codigo',
                        'tr.estado AS cestado','tr.id',
                        DB::raw(
                            'IF(tr.estado,"Activo","Desactivo") AS estado'
                        )
                    )
                    ->where(
                        function($query){
                            if( Input::get('estado') ){
                                $query->where('tr.estado', '=', Input::get('estado'));
                            }
                        }
                    )
                    ->orderBy('s.nombre')
                    ->get();

        return $tr;
    }

    public function getRelacionunico()
    {
        $tr=         DB::table('tablas_relacion AS tr')
                    ->join(
                        'softwares AS s',
                        's.id', '=', 'tr.software_id'
                    )
                    ->leftJoin(
                        'rutas AS r',
                        'tr.id', '=', 'r.tabla_relacion_id'
                    )
                    ->select(
                        's.nombre AS software','tr.id_union AS codigo',
                        'tr.estado AS cestado','tr.id',
                        DB::raw(
                            'IF(tr.estado,"Activo","Desactivo") AS estado,
                            count(r.id) AS count'
                        )
                    )
                    ->where(
                        function($query){
                            $query->where('r.estado', '=', '1');
                            if( Input::get('estado') ){
                                $query->where('tr.estado', '=', Input::get('estado'));
                            }
                        }
                    )
                    ->groupBy('tr.id')
                    ->having('count', '=', '0')
                    ->get();

        return $tr;
    }

    public function guardarRelacion()
    {
        $tr = new TablaRelacion;
        $tr['software_id']= Input::get('software_id');
        $tr['id_union']= Input::get('codigo');
        $tr['usuario_created_at'] = Auth::user()->id;
        $tr->save();

        return $tr;
    }
}
