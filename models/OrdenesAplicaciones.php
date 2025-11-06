<?php

namespace Model;

class OrdenesAplicaciones extends ActiveRecord
{
    protected static $tabla = 'ordenes_aplicaciones';
    public static $idTabla = 'id_ordenes_aplicaciones';
    protected static $columnasDB = [
        'ordenes_aplicaciones_codigo',
        'ordenes_aplicaciones_fecha_asignacion',
        'ordenes_aplicaciones_fecha_entrega',
        'ordenes_aplicaciones_notas',
        'ordenes_aplicaciones_situacion',
        'estados_id_estados',
        'aplicacion_id_aplicacion',
        'usuarios_id_usuarios'
    ];

    public $id_ordenes_aplicaciones;
    public $ordenes_aplicaciones_codigo;
    public $ordenes_aplicaciones_fecha_asignacion;
    public $ordenes_aplicaciones_fecha_entrega;
    public $ordenes_aplicaciones_notas;
    public $ordenes_aplicaciones_situacion;
    public $estados_id_estados;
    public $aplicacion_id_aplicacion;
    public $usuarios_id_usuarios;

    public function __construct($args = [])
    {
        $this->id_ordenes_aplicaciones = $args['id_ordenes_aplicaciones'] ?? null;
        $this->ordenes_aplicaciones_codigo = $args['ordenes_aplicaciones_codigo'] ?? '';
        $this->ordenes_aplicaciones_fecha_asignacion = $args['ordenes_aplicaciones_fecha_asignacion'] ?? date('Y-m-d H:i:s');
        $this->ordenes_aplicaciones_fecha_entrega = $args['ordenes_aplicaciones_fecha_entrega'] ?? '';
        $this->ordenes_aplicaciones_notas = $args['ordenes_aplicaciones_notas'] ?? '';
        $this->ordenes_aplicaciones_situacion = $args['ordenes_aplicaciones_situacion'] ?? 1;
        $this->estados_id_estados = $args['estados_id_estados'] ?? null;
        $this->aplicacion_id_aplicacion = $args['aplicacion_id_aplicacion'] ?? null;
        $this->usuarios_id_usuarios = $args['usuarios_id_usuarios'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->ordenes_aplicaciones_codigo) {
            self::setAlerta('error', 'El código del proyecto es obligatorio');
        }

        if (!$this->ordenes_aplicaciones_fecha_entrega) {
            self::setAlerta('error', 'La fecha de entrega es obligatoria');
        }

        if (!$this->estados_id_estados) {
            self::setAlerta('error', 'El estado del proyecto es obligatorio');
        }

        if (!$this->aplicacion_id_aplicacion) {
            self::setAlerta('error', 'Debe seleccionar una aplicación');
        }

        if (!$this->usuarios_id_usuarios) {
            self::setAlerta('error', 'Debe asignar un programador responsable');
        }

        return self::getAlertas();
    }

    public static function generarCodigo()
    {
        $fecha = date('Ymd');
        $query = "SELECT MAX(ordenes_aplicaciones_codigo) as ultimo FROM " . static::$tabla .
            " WHERE ordenes_aplicaciones_codigo LIKE '{$fecha}%'";
        $resultado = self::$db->query($query);
        $row = $resultado->fetch();

        if ($row['ultimo']) {
            $numero = intval(substr($row['ultimo'], 8)) + 1;
            return $fecha . str_pad($numero, 3, '0', STR_PAD_LEFT);
        }

        return $fecha . '001';
    }
}
