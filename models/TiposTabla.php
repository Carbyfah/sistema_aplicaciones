<?php

namespace Model;

class TiposTabla extends ActiveRecord
{
    protected static $tabla = 'tipos_tabla';
    public static $idTabla = 'id_tipo_tabla';
    protected static $columnasDB = [
        'tipos_tabla_nombre',
        'tipos_tabla_descripcion',
        'tipos_tabla_situacion'
    ];

    public $id_tipo_tabla;
    public $tipos_tabla_nombre;
    public $tipos_tabla_descripcion;
    public $tipos_tabla_situacion;

    public function __construct($args = [])
    {
        $this->id_tipo_tabla = $args['id_tipo_tabla'] ?? null;
        $this->tipos_tabla_nombre = $args['tipos_tabla_nombre'] ?? '';
        $this->tipos_tabla_descripcion = $args['tipos_tabla_descripcion'] ?? '';
        $this->tipos_tabla_situacion = $args['tipos_tabla_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->tipos_tabla_nombre) {
            self::setAlerta('error', 'El nombre del tipo de tabla es obligatorio');
        }

        return self::getAlertas();
    }
}
