<?php

namespace Model;

class LogsActividad extends ActiveRecord
{
    protected static $tabla = 'logs_actividad';
    public static $idTabla = 'id_logs_actividad';
    protected static $columnasDB = [
        'logs_actividad_accion',
        'logs_actividad_tabla',
        'logs_actividad_registro_id',
        'logs_actividad_datos_antiguos',
        'logs_actividad_datos_nuevos',
        'logs_actividad_ip',
        'logs_actividad_fecha',
        'logs_actividad_situacion',
        'persona_id_persona'
    ];

    public $id_logs_actividad;
    public $logs_actividad_accion;
    public $logs_actividad_tabla;
    public $logs_actividad_registro_id;
    public $logs_actividad_datos_antiguos;
    public $logs_actividad_datos_nuevos;
    public $logs_actividad_ip;
    public $logs_actividad_fecha;
    public $logs_actividad_situacion;
    public $persona_id_persona;

    public function __construct($args = [])
    {
        $this->id_logs_actividad = $args['id_logs_actividad'] ?? null;
        $this->logs_actividad_accion = $args['logs_actividad_accion'] ?? '';
        $this->logs_actividad_tabla = $args['logs_actividad_tabla'] ?? '';
        $this->logs_actividad_registro_id = $args['logs_actividad_registro_id'] ?? null;
        $this->logs_actividad_datos_antiguos = $args['logs_actividad_datos_antiguos'] ?? null;
        $this->logs_actividad_datos_nuevos = $args['logs_actividad_datos_nuevos'] ?? null;
        $this->logs_actividad_ip = $args['logs_actividad_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $this->logs_actividad_fecha = $args['logs_actividad_fecha'] ?? date('Y-m-d H:i:s');
        $this->logs_actividad_situacion = $args['logs_actividad_situacion'] ?? 1;
        $this->persona_id_persona = $args['persona_id_persona'] ?? null;
    }

    public static function registrar($accion, $tabla, $registro_id, $datos_antiguos, $datos_nuevos, $persona_id)
    {
        $log = new self([
            'logs_actividad_accion' => $accion,
            'logs_actividad_tabla' => $tabla,
            'logs_actividad_registro_id' => $registro_id,
            'logs_actividad_datos_antiguos' => is_string($datos_antiguos) ? $datos_antiguos : json_encode($datos_antiguos),
            'logs_actividad_datos_nuevos' => is_string($datos_nuevos) ? $datos_nuevos : json_encode($datos_nuevos),
            'persona_id_persona' => $persona_id
        ]);

        return $log->guardar();
    }
}
