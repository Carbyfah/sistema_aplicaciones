<?php

namespace Model;

class Aplicacion extends ActiveRecord
{
    protected static $tabla = 'aplicacion';
    public static $idTabla = 'id_aplicacion';
    protected static $columnasDB = [
        'aplicacion_nombre',
        'aplicacion_desc_corta',
        'aplicacion_larga',
        'aplicacion_situacion',
        'fecha_creacion',
        'fecha_modificacion',
        'creado_por',
        'modificado_por'
    ];

    public $id_aplicacion;
    public $aplicacion_nombre;
    public $aplicacion_desc_corta;
    public $aplicacion_larga;
    public $aplicacion_situacion;
    public $fecha_creacion;
    public $fecha_modificacion;
    public $creado_por;
    public $modificado_por;

    public function __construct($args = [])
    {
        $this->id_aplicacion = $args['id_aplicacion'] ?? null;
        $this->aplicacion_nombre = $args['aplicacion_nombre'] ?? '';
        $this->aplicacion_desc_corta = $args['aplicacion_desc_corta'] ?? '';
        $this->aplicacion_larga = $args['aplicacion_larga'] ?? '';
        $this->aplicacion_situacion = $args['aplicacion_situacion'] ?? 1;
        $this->fecha_creacion = $args['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $this->fecha_modificacion = $args['fecha_modificacion'] ?? null;
        $this->creado_por = $args['creado_por'] ?? null;
        $this->modificado_por = $args['modificado_por'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->aplicacion_nombre) {
            self::setAlerta('error', 'El nombre del proyecto es obligatorio');
        }

        return self::getAlertas();
    }
}
