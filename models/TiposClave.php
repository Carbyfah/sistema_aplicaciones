<?php

namespace Model;

class TiposClave extends ActiveRecord
{
    protected static $tabla = 'tipos_clave';
    public static $idTabla = 'id_tipo_clave';
    protected static $columnasDB = [
        'tipos_clave_nombre',
        'tipos_clave_descripcion',
        'tipos_clave_situacion'
    ];

    public $id_tipo_clave;
    public $tipos_clave_nombre;
    public $tipos_clave_descripcion;
    public $tipos_clave_situacion;

    public function __construct($args = [])
    {
        $this->id_tipo_clave = $args['id_tipo_clave'] ?? null;
        $this->tipos_clave_nombre = $args['tipos_clave_nombre'] ?? '';
        $this->tipos_clave_descripcion = $args['tipos_clave_descripcion'] ?? '';
        $this->tipos_clave_situacion = $args['tipos_clave_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->tipos_clave_nombre) {
            self::setAlerta('error', 'El nombre del tipo de clave es obligatorio');
        }

        return self::getAlertas();
    }
}
