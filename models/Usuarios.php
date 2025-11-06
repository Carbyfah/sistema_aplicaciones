<?php

namespace Model;

class Usuarios extends ActiveRecord
{
    protected static $tabla = 'usuarios';
    public static $idTabla = 'id_usuarios';
    protected static $columnasDB = [
        'usuarios_nombre',
        'usuarios_password',
        'usuarios_situacion',
        'ultimo_acceso',
        'token_recuperacion',
        'token_expiracion',
        'persona_id_persona'
    ];

    public $id_usuarios;
    public $usuarios_nombre;
    public $usuarios_password;
    public $usuarios_situacion;
    public $ultimo_acceso;
    public $token_recuperacion;
    public $token_expiracion;
    public $persona_id_persona;

    public function __construct($args = [])
    {
        $this->id_usuarios = $args['id_usuarios'] ?? null;
        $this->usuarios_nombre = $args['usuarios_nombre'] ?? '';
        $this->usuarios_password = $args['usuarios_password'] ?? '';
        $this->usuarios_situacion = $args['usuarios_situacion'] ?? 1;
        $this->ultimo_acceso = $args['ultimo_acceso'] ?? null;
        $this->token_recuperacion = $args['token_recuperacion'] ?? null;
        $this->token_expiracion = $args['token_expiracion'] ?? null;
        $this->persona_id_persona = $args['persona_id_persona'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->usuarios_nombre) {
            self::setAlerta('error', 'El nombre de usuario es obligatorio');
        }

        if (!$this->usuarios_password) {
            self::setAlerta('error', 'La contraseña es obligatoria');
        }

        if (!$this->persona_id_persona) {
            self::setAlerta('error', 'El usuario debe estar asociado a una persona');
        }

        return self::getAlertas();
    }

    public function comprobarPassword($password)
    {
        return password_verify($password, $this->usuarios_password);
    }

    public function hashPassword()
    {
        $this->usuarios_password = password_hash($this->usuarios_password, PASSWORD_BCRYPT);
    }

    public function generarToken()
    {
        $this->token_recuperacion = uniqid();
        $this->token_expiracion = date('Y-m-d H:i:s', strtotime('+1 day'));
    }
}
