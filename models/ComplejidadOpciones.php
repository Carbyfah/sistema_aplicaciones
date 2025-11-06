<?php

namespace Model;

class ComplejidadOpciones extends ActiveRecord
{
    protected static $tabla = 'complejidad_opciones';
    public static $idTabla = 'id_complejidad';
    protected static $columnasDB = [
        'complejidad_nombre',
        'complejidad_descripcion',
        'complejidad_factor',
        'complejidad_situacion'
    ];

    public $id_complejidad;
    public $complejidad_nombre;
    public $complejidad_descripcion;
    public $complejidad_factor;
    public $complejidad_situacion;

    public function __construct($args = [])
    {
        $this->id_complejidad = $args['id_complejidad'] ?? null;
        $this->complejidad_nombre = $args['complejidad_nombre'] ?? '';
        $this->complejidad_descripcion = $args['complejidad_descripcion'] ?? '';
        $this->complejidad_factor = $args['complejidad_factor'] ?? 1.00;
        $this->complejidad_situacion = $args['complejidad_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->complejidad_nombre) {
            self::setAlerta('error', 'El nombre de la complejidad es obligatorio');
        }

        if (!$this->complejidad_factor || $this->complejidad_factor <= 0) {
            self::setAlerta('error', 'El factor debe ser mayor a 0');
        }

        return self::getAlertas();
    }
}
