<?php
class FlujoTipoRespuesta extends Base
{
    public $table = "flujo_tipo_respuesta";
    public static $where = ['id', 'dtiempo', 'flujo_id', 'tipo_respuesta_id',
     'tiempo_id', 'estado'];
    public static $selec = ['id', 'dtiempo', 'flujo_id', 'tipo_respuesta_id',
     'tiempo_id', 'estado'];
    
    /**
     * Flujo relationship
     */
    public function flujo()
    {
        return $this->belongsTo('Flujo');
    }
    public static function getFlujoTipoRsp()
    {
        return DB::table('flujo_tipo_respuesta as ftr')
                    ->join('flujos as f', 'ftr.flujo_id', '=', 'f.id')
                    ->join('tipos_respuesta as tr', 'ftr.tipo_respuesta_id', '=', 'tr.id')
                    ->join('tiempos as t', 'ftr.tiempo_id', '=', 't.id')
                    ->select(
                        'ftr.id',
                        'ftr.dtiempo',
                        'ftr.estado',
                        'f.nombre as flujo',
                        'ftr.flujo_id',
                        'tr.nombre as tipo_respuesta',
                        'ftr.tipo_respuesta_id',
                        't.nombre as tiempo',
                        'ftr.tiempo_id'
                    )
                    ->get();
    }
}
