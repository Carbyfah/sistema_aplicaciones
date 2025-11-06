<?php

namespace Model;

class SeguridadOpciones extends ActiveRecord
{
    protected static $tabla = 'seguridad_opciones';
    public static $idTabla = 'id_seguridad';
    protected static $columnasDB = [
        'seguridad_nombre',
        'seguridad_descripcion',
        'seguridad_factor',
        'seguridad_situacion'
    ];

    public $id_seguridad;
    public $seguridad_nombre;
    public $seguridad_descripcion;
    public $seguridad_factor;
    public $seguridad_situacion;

    public function __construct($args = [])
    {
        $this->id_seguridad = $args['id_seguridad'] ?? null;
        $this->seguridad_nombre = $args['seguridad_nombre'] ?? '';
        $this->seguridad_descripcion = $args['seguridad_descripcion'] ?? '';
        $this->seguridad_factor = $args['seguridad_factor'] ?? 1.00;
        $this->seguridad_situacion = $args['seguridad_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->seguridad_nombre) {
            self::setAlerta('error', 'El nombre del nivel de seguridad es obligatorio');
        }

        if (!$this->seguridad_factor || $this->seguridad_factor <= 0) {
            self::setAlerta('error', 'El factor debe ser mayor a 0');
        }

        return self::getAlertas();
    }
}
