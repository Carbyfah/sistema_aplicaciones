<?php

namespace Model;

class AplicacionTablas extends ActiveRecord
{
    protected static $tabla = 'aplicacion_tablas';
    public static $idTabla = 'id_aplicacion_tablas';
    protected static $columnasDB = [
        'aplicacion_id_aplicacion',
        'tablas_nombre',
        'tablas_descripcion',
        'tipo_tabla_id',
        'tablas_situacion',
        'fecha_creacion',
        'fecha_modificacion',
        'creado_por',
        'modificado_por'
    ];

    public $id_aplicacion_tablas;
    public $aplicacion_id_aplicacion;
    public $tablas_nombre;
    public $tablas_descripcion;
    public $tipo_tabla_id;
    public $tablas_situacion;
    public $fecha_creacion;
    public $fecha_modificacion;
    public $creado_por;
    public $modificado_por;

    public function __construct($args = [])
    {
        $this->id_aplicacion_tablas = $args['id_aplicacion_tablas'] ?? null;
        $this->aplicacion_id_aplicacion = $args['aplicacion_id_aplicacion'] ?? null;
        $this->tablas_nombre = $args['tablas_nombre'] ?? '';
        $this->tablas_descripcion = $args['tablas_descripcion'] ?? '';
        $this->tipo_tabla_id = $args['tipo_tabla_id'] ?? null;
        $this->tablas_situacion = $args['tablas_situacion'] ?? 1;
        $this->fecha_creacion = $args['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $this->fecha_modificacion = $args['fecha_modificacion'] ?? null;
        $this->creado_por = $args['creado_por'] ?? $_SESSION['usuario_id'] ?? null;
        $this->modificado_por = $args['modificado_por'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->aplicacion_id_aplicacion) {
            self::setAlerta('error', 'La aplicación es obligatoria');
        }

        if (!$this->tablas_nombre) {
            self::setAlerta('error', 'El nombre de la tabla es obligatorio');
        }

        return self::getAlertas();
    }
}
