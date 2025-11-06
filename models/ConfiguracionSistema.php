<?php

namespace Model;

class ConfiguracionSistema extends ActiveRecord
{
    protected static $tabla = 'configuracion_sistema';
    public static $idTabla = 'id_configuracion_sistema';
    protected static $columnasDB = [
        'configuracion_sistema_clave',
        'configuracion_sistema_valor',
        'configuracion_sistema_tipo',
        'configuracion_sistema_descripcion',
        'configuracion_sistema_situacion'
    ];

    public $id_configuracion_sistema;
    public $configuracion_sistema_clave;
    public $configuracion_sistema_valor;
    public $configuracion_sistema_tipo;
    public $configuracion_sistema_descripcion;
    public $configuracion_sistema_situacion;

    public function __construct($args = [])
    {
        $this->id_configuracion_sistema = $args['id_configuracion_sistema'] ?? null;
        $this->configuracion_sistema_clave = $args['configuracion_sistema_clave'] ?? '';
        $this->configuracion_sistema_valor = $args['configuracion_sistema_valor'] ?? '';
        $this->configuracion_sistema_tipo = $args['configuracion_sistema_tipo'] ?? 'string';
        $this->configuracion_sistema_descripcion = $args['configuracion_sistema_descripcion'] ?? '';
        $this->configuracion_sistema_situacion = $args['configuracion_sistema_situacion'] ?? 1;
    }

    public static function getValor($clave)
    {
        $config = static::where('configuracion_sistema_clave', $clave);
        if (empty($config)) {
            return null;
        }
        return $config[0]->configuracion_sistema_valor;
    }
}
