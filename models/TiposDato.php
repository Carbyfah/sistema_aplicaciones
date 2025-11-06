<?php

namespace Model;

class TiposDato extends ActiveRecord
{
    protected static $tabla = 'tipos_dato';
    public static $idTabla = 'id_tipo_dato';
    protected static $columnasDB = [
        'tipos_dato_nombre',
        'tipos_dato_descripcion',
        'tipos_dato_situacion'
    ];

    public $id_tipo_dato;
    public $tipos_dato_nombre;
    public $tipos_dato_descripcion;
    public $tipos_dato_situacion;

    public function __construct($args = [])
    {
        $this->id_tipo_dato = $args['id_tipo_dato'] ?? null;
        $this->tipos_dato_nombre = $args['tipos_dato_nombre'] ?? '';
        $this->tipos_dato_descripcion = $args['tipos_dato_descripcion'] ?? '';
        $this->tipos_dato_situacion = $args['tipos_dato_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->tipos_dato_nombre) {
            self::setAlerta('error', 'El nombre del tipo de dato es obligatorio');
        }

        return self::getAlertas();
    }
}
