<?php

namespace Model;

class TareasAplicaciones extends ActiveRecord
{
    protected static $tabla = 'tareas_aplicaciones';
    public static $idTabla = 'id_tareas_aplicaciones';
    protected static $columnasDB = [
        'tareas_aplicaciones_titulo',
        'tareas_aplicaciones_descripcion',
        'tareas_aplicaciones_completada',
        'tareas_aplicaciones_fecha_limite',
        'tareas_aplicaciones_fecha_completada',
        'tareas_aplicaciones_prioridad',
        'tareas_aplicaciones_situacion',
        'ordenes_aplicaciones_id_ordenes_aplicaciones',
        'usuarios_id_usuarios'
    ];

    public $id_tareas_aplicaciones;
    public $tareas_aplicaciones_titulo;
    public $tareas_aplicaciones_descripcion;
    public $tareas_aplicaciones_completada;
    public $tareas_aplicaciones_fecha_limite;
    public $tareas_aplicaciones_fecha_completada;
    public $tareas_aplicaciones_prioridad;
    public $tareas_aplicaciones_situacion;
    public $ordenes_aplicaciones_id_ordenes_aplicaciones;
    public $usuarios_id_usuarios;

    public function __construct($args = [])
    {
        $this->id_tareas_aplicaciones = $args['id_tareas_aplicaciones'] ?? null;
        $this->tareas_aplicaciones_titulo = $args['tareas_aplicaciones_titulo'] ?? '';
        $this->tareas_aplicaciones_descripcion = $args['tareas_aplicaciones_descripcion'] ?? '';
        $this->tareas_aplicaciones_completada = $args['tareas_aplicaciones_completada'] ?? 0;
        $this->tareas_aplicaciones_fecha_limite = $args['tareas_aplicaciones_fecha_limite'] ?? null;
        $this->tareas_aplicaciones_fecha_completada = $args['tareas_aplicaciones_fecha_completada'] ?? null;
        $this->tareas_aplicaciones_prioridad = $args['tareas_aplicaciones_prioridad'] ?? 'Media';
        $this->tareas_aplicaciones_situacion = $args['tareas_aplicaciones_situacion'] ?? 1;
        $this->ordenes_aplicaciones_id_ordenes_aplicaciones = $args['ordenes_aplicaciones_id_ordenes_aplicaciones'] ?? null;
        $this->usuarios_id_usuarios = $args['usuarios_id_usuarios'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->tareas_aplicaciones_titulo) {
            self::setAlerta('error', 'El título de la tarea es obligatorio');
        }

        if (!$this->ordenes_aplicaciones_id_ordenes_aplicaciones) {
            self::setAlerta('error', 'La tarea debe estar asociada a un proyecto');
        }

        return self::getAlertas();
    }

    public function marcarCompletada()
    {
        $this->tareas_aplicaciones_completada = 1;
        $this->tareas_aplicaciones_fecha_completada = date('Y-m-d H:i:s');
        return $this->guardar();
    }

    public static function tareasPorProyecto($proyecto_id)
    {
        return static::where('ordenes_aplicaciones_id_ordenes_aplicaciones', $proyecto_id);
    }
}
