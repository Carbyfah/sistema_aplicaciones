<?php

namespace Model;

class PersonalProyecto extends ActiveRecord
{
    protected static $tabla = 'personal_proyecto';
    public static $idTabla = 'id_personal_proyecto';
    protected static $columnasDB = [
        'personal_proyecto_rol',
        'personal_proyecto_fecha_asignacion',
        'personal_proyecto_situacion',
        'persona_id_persona',
        'ordenes_aplicaciones_id_ordenes_aplicaciones'
    ];

    public $id_personal_proyecto;
    public $personal_proyecto_rol;
    public $personal_proyecto_fecha_asignacion;
    public $personal_proyecto_situacion;
    public $persona_id_persona;
    public $ordenes_aplicaciones_id_ordenes_aplicaciones;

    public function __construct($args = [])
    {
        $this->id_personal_proyecto = $args['id_personal_proyecto'] ?? null;
        $this->personal_proyecto_rol = $args['personal_proyecto_rol'] ?? null;
        $this->personal_proyecto_fecha_asignacion = $args['personal_proyecto_fecha_asignacion'] ?? date('Y-m-d H:i:s');
        $this->personal_proyecto_situacion = $args['personal_proyecto_situacion'] ?? 1;
        $this->persona_id_persona = $args['persona_id_persona'] ?? null;
        $this->ordenes_aplicaciones_id_ordenes_aplicaciones = $args['ordenes_aplicaciones_id_ordenes_aplicaciones'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->ordenes_aplicaciones_id_ordenes_aplicaciones) {
            self::setAlerta('error', 'Debe seleccionar un proyecto');
        }

        if (!$this->persona_id_persona) {
            self::setAlerta('error', 'Debe seleccionar una persona');
        }

        return self::getAlertas();
    }

    public static function personalPorProyecto($proyecto_id)
    {
        return static::where('ordenes_aplicaciones_id_ordenes_aplicaciones', $proyecto_id);
    }

    public static function proyectosPorPersona($persona_id)
    {
        return static::where('persona_id_persona', $persona_id);
    }
}
