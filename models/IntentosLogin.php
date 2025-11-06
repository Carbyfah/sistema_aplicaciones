<?php

namespace Model;

class IntentosLogin extends ActiveRecord
{
    protected static $tabla = 'intentos_login';
    public static $idTabla = 'id_intento_login';
    protected static $columnasDB = [
        'usuario_nombre',
        'intento_exitoso',
        'intento_detalle',
        'intento_ip',
        'intento_fecha'
    ];

    public $id_intento_login;
    public $usuario_nombre;
    public $intento_exitoso;
    public $intento_detalle;
    public $intento_ip;
    public $intento_fecha;

    public function __construct($args = [])
    {
        $this->id_intento_login = $args['id_intento_login'] ?? null;
        $this->usuario_nombre = $args['usuario_nombre'] ?? '';
        $this->intento_exitoso = $args['intento_exitoso'] ?? 0;
        $this->intento_detalle = $args['intento_detalle'] ?? '';
        $this->intento_ip = $args['intento_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $this->intento_fecha = $args['intento_fecha'] ?? date('Y-m-d H:i:s');
    }

    public static function registrarIntento($usuario, $exitoso, $detalle = '')
    {
        $intento = new self([
            'usuario_nombre' => $usuario,
            'intento_exitoso' => $exitoso ? 1 : 0,
            'intento_detalle' => $detalle
        ]);

        return $intento->guardar();
    }

    public static function getIntentosUsuario($usuario, $limite = null)
    {
        $query = "SELECT * FROM " . static::$tabla .
            " WHERE usuario_nombre = " . self::$db->quote($usuario) .
            " ORDER BY intento_fecha DESC";

        if ($limite) {
            $query .= " LIMIT " . intval($limite);
        }

        return self::consultarSQL($query);
    }

    public static function getIntentosFallidos($usuario, $minutos = 30)
    {
        $query = "SELECT COUNT(*) as total FROM " . static::$tabla .
            " WHERE usuario_nombre = " . self::$db->quote($usuario) .
            " AND intento_exitoso = 0" .
            " AND intento_fecha > DATE_SUB(NOW(), INTERVAL " . intval($minutos) . " MINUTE)";

        $resultado = self::$db->query($query);
        $fila = $resultado->fetch();
        return (int)$fila['total'];
    }
}
