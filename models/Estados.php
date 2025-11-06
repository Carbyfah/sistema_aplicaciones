<?php

namespace Model;

class Estados extends ActiveRecord
{
    protected static $tabla = 'estados';
    public static $idTabla = 'id_estados';
    protected static $columnasDB = [
        'estados_nombre',
        'estados_descripcion',
        'estados_color',
        'estados_situacion'
    ];

    public $id_estados;
    public $estados_nombre;
    public $estados_descripcion;
    public $estados_color;
    public $estados_situacion;

    public function __construct($args = [])
    {
        $this->id_estados = $args['id_estados'] ?? null;
        $this->estados_nombre = $args['estados_nombre'] ?? '';
        $this->estados_descripcion = $args['estados_descripcion'] ?? '';
        $this->estados_color = $args['estados_color'] ?? '#3788d8';
        $this->estados_situacion = $args['estados_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->estados_nombre) {
            self::setAlerta('error', 'El nombre del estado es obligatorio');
        }

        return self::getAlertas();
    }
}
