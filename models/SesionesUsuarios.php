<?php

namespace Model;

class SesionesUsuarios extends ActiveRecord
{
    protected static $tabla = 'sesiones_usuarios';
    public static $idTabla = 'sesion_id';
    protected static $columnasDB = [
        'sesion_token',
        'sesion_fecha_inicio',
        'sesion_fecha_cierre',
        'sesion_ip',
        'sesion_user_agent',
        'sesion_estado',
        'usuarios_id_usuarios'
    ];

    public $sesion_id;
    public $sesion_token;
    public $sesion_fecha_inicio;
    public $sesion_fecha_cierre;
    public $sesion_ip;
    public $sesion_user_agent;
    public $sesion_estado;
    public $usuarios_id_usuarios;

    public function __construct($args = [])
    {
        $this->sesion_id = $args['sesion_id'] ?? null;
        $this->sesion_token = $args['sesion_token'] ?? bin2hex(random_bytes(32));
        $this->sesion_fecha_inicio = $args['sesion_fecha_inicio'] ?? date('Y-m-d H:i:s');
        $this->sesion_fecha_cierre = $args['sesion_fecha_cierre'] ?? null;
        $this->sesion_ip = $args['sesion_ip'] ?? $_SERVER['REMOTE_ADDR'] ?? '';
        $this->sesion_user_agent = $args['sesion_user_agent'] ?? $_SERVER['HTTP_USER_AGENT'] ?? '';
        $this->sesion_estado = $args['sesion_estado'] ?? 1;
        $this->usuarios_id_usuarios = $args['usuarios_id_usuarios'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->usuarios_id_usuarios) {
            self::setAlerta('error', 'La sesión debe estar asociada a un usuario');
        }

        if (!$this->sesion_token) {
            self::setAlerta('error', 'El token de sesión es obligatorio');
        }

        return self::getAlertas();
    }

    public function cerrarSesion()
    {
        $this->sesion_fecha_cierre = date('Y-m-d H:i:s');
        $this->sesion_estado = 0;
        return $this->guardar();
    }

    public static function getSesionesUsuario($usuario_id)
    {
        return static::where('usuarios_id_usuarios', $usuario_id);
    }

    public static function getSesionPorToken($token)
    {
        $sesiones = static::where('sesion_token', $token);
        if (empty($sesiones)) {
            return null;
        }
        return $sesiones[0];
    }

    public static function cerrarSesionesPorToken($token)
    {
        $query = "UPDATE " . static::$tabla .
            " SET sesion_estado = 0, sesion_fecha_cierre = NOW()" .
            " WHERE sesion_token = " . self::$db->quote($token);

        return self::SQL($query);
    }
}
