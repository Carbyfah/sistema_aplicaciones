<?php

namespace Model;

class Persona extends ActiveRecord
{
    protected static $tabla = 'persona';
    public static $idTabla = 'id_persona';
    protected static $columnasDB = [
        'persona_nombres',
        'persona_apellidos',
        'persona_identidad',
        'persona_telefono',
        'persona_correo',
        'persona_situacion',
        'roles_persona_id_roles_persona'
    ];

    public $id_persona;
    public $persona_nombres;
    public $persona_apellidos;
    public $persona_identidad;
    public $persona_telefono;
    public $persona_correo;
    public $persona_situacion;
    public $roles_persona_id_roles_persona;

    public function __construct($args = [])
    {
        $this->id_persona = $args['id_persona'] ?? null;
        $this->persona_nombres = $args['persona_nombres'] ?? '';
        $this->persona_apellidos = $args['persona_apellidos'] ?? '';
        $this->persona_identidad = $args['persona_identidad'] ?? '';
        $this->persona_telefono = $args['persona_telefono'] ?? '';
        $this->persona_correo = $args['persona_correo'] ?? '';
        $this->persona_situacion = $args['persona_situacion'] ?? 1;
        $this->roles_persona_id_roles_persona = $args['roles_persona_id_roles_persona'] ?? null;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->persona_nombres) {
            self::setAlerta('error', 'El nombre es obligatorio');
        }

        if (!$this->persona_apellidos) {
            self::setAlerta('error', 'Los apellidos son obligatorios');
        }

        if (!$this->persona_identidad) {
            self::setAlerta('error', 'La identificación es obligatoria');
        }

        return self::getAlertas();
    }

    public function getNombreCompleto()
    {
        return $this->persona_nombres . ' ' . $this->persona_apellidos;
    }
}
