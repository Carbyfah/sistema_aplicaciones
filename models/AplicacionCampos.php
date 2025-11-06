<?php

namespace Model;

class AplicacionCampos extends ActiveRecord
{
    protected static $tabla = 'aplicacion_campos';
    public static $idTabla = 'id_aplicacion_campos';
    protected static $columnasDB = [
        'aplicacion_tablas_id',
        'campos_nombre',
        'tipo_dato_id',
        'campos_longitud',
        'campos_nulo',
        'tipo_clave_id',
        'campos_descripcion',
        'campos_situacion',
        'fecha_creacion',
        'fecha_modificacion',
        'creado_por',
        'modificado_por'
    ];

    public $id_aplicacion_campos;
    public $aplicacion_tablas_id;
    public $campos_nombre;
    public $tipo_dato_id;
    public $campos_longitud;
    public $campos_nulo;
    public $tipo_clave_id;
    public $campos_descripcion;
    public $campos_situacion;
    public $fecha_creacion;
    public $fecha_modificacion;
    public $creado_por;
    public $modificado_por;

    public function __construct($args = [])
    {
        $this->id_aplicacion_campos = $args['id_aplicacion_campos'] ?? null;
        $this->aplicacion_tablas_id = $args['aplicacion_tablas_id'] ?? null;
        $this->campos_nombre = $args['campos_nombre'] ?? '';
        $this->tipo_dato_id = $args['tipo_dato_id'] ?? null;
        $this->campos_longitud = $args['campos_longitud'] ?? null;
        $this->campos_nulo = $args['campos_nulo'] ?? 0;
        $this->tipo_clave_id = $args['tipo_clave_id'] ?? null;
        $this->campos_descripcion = $args['campos_descripcion'] ?? '';
        $this->campos_situacion = $args['campos_situacion'] ?? 1;
        $this->fecha_creacion = $args['fecha_creacion'] ?? date('Y-m-d H:i:s');
        $this->fecha_modificacion = $args['fecha_modificacion'] ?? null;
        $this->creado_por = $args['creado_por'] ?? $_SESSION['usuario_id'] ?? null;
        $this->modificado_por = $args['modificado_por'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->aplicacion_tablas_id) {
            self::setAlerta('error', 'La tabla es obligatoria');
        }

        if (!$this->campos_nombre) {
            self::setAlerta('error', 'El nombre del campo es obligatorio');
        }

        if (!$this->tipo_dato_id) {
            self::setAlerta('error', 'El tipo de dato es obligatorio');
        }

        return self::getAlertas();
    }
}
