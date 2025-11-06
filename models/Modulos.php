<?php
// models/Modulos.php
namespace Model;

class Modulos extends ActiveRecord
{
    protected static $tabla = 'modulos';
    public static $idTabla = 'id_modulos';
    protected static $columnasDB = [
        'modulos_nombre',
        'modulos_descripcion',
        'modulo_padre_id',
        'modulos_situacion'
    ];

    public $id_modulos;
    public $modulos_nombre;
    public $modulos_descripcion;
    public $modulo_padre_id;
    public $modulos_situacion;

    public function __construct($args = [])
    {
        $this->id_modulos = $args['id_modulos'] ?? null;
        $this->modulos_nombre = $args['modulos_nombre'] ?? '';
        $this->modulos_descripcion = $args['modulos_descripcion'] ?? '';
        $this->modulo_padre_id = $args['modulo_padre_id'] ?? null;
        $this->modulos_situacion = $args['modulos_situacion'] ?? 1;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->modulos_nombre) {
            self::setAlerta('error', 'El nombre del módulo es obligatorio');
        }

        return self::getAlertas();
    }
}
