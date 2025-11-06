<?php

namespace Model;

class Notificaciones extends ActiveRecord
{
    protected static $tabla = 'notificaciones';
    public static $idTabla = 'id_notificaciones';
    protected static $columnasDB = [
        'notificaciones_titulo',
        'notificaciones_mensaje',
        'notificaciones_leida',
        'notificaciones_tipo',
        'notificaciones_objeto_id',
        'notificaciones_objeto_tipo',
        'notificaciones_fecha',
        'notificaciones_situacion',
        'persona_id_persona'
    ];

    public $id_notificaciones;
    public $notificaciones_titulo;
    public $notificaciones_mensaje;
    public $notificaciones_leida;
    public $notificaciones_tipo;
    public $notificaciones_objeto_id;
    public $notificaciones_objeto_tipo;
    public $notificaciones_fecha;
    public $notificaciones_situacion;
    public $persona_id_persona;

    public function __construct($args = [])
    {
        $this->id_notificaciones = $args['id_notificaciones'] ?? null;
        $this->notificaciones_titulo = $args['notificaciones_titulo'] ?? '';
        $this->notificaciones_mensaje = $args['notificaciones_mensaje'] ?? '';
        $this->notificaciones_leida = $args['notificaciones_leida'] ?? 0;
        $this->notificaciones_tipo = $args['notificaciones_tipo'] ?? 'INFO';
        $this->notificaciones_objeto_id = $args['notificaciones_objeto_id'] ?? null;
        $this->notificaciones_objeto_tipo = $args['notificaciones_objeto_tipo'] ?? null;
        $this->notificaciones_fecha = $args['notificaciones_fecha'] ?? date('Y-m-d H:i:s');
        $this->notificaciones_situacion = $args['notificaciones_situacion'] ?? 1;
        $this->persona_id_persona = $args['persona_id_persona'] ?? null;
    }

    public static function getNotificacionesUsuario($persona_id)
    {
        return static::where('persona_id_persona', $persona_id);
    }

    public static function getNoLeidas($persona_id)
    {
        return static::where('persona_id_persona', $persona_id, ' = ');
    }

    public function marcarLeida()
    {
        $this->notificaciones_leida = 1;
        return $this->guardar();
    }
}
