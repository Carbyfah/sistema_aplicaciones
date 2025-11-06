<?php

namespace Model;

class RolesPersona extends ActiveRecord
{
    protected static $tabla = 'roles_persona';
    public static $idTabla = 'id_roles_persona';
    protected static $columnasDB = [
        'roles_persona_nombre',
        'roles_persona_descripcion',
        'roles_persona_situacion'
    ];

    public $id_roles_persona;
    public $roles_persona_nombre;
    public $roles_persona_descripcion;
    public $roles_persona_situacion;

    public function __construct($args = [])
    {
        $this->id_roles_persona = $args['id_roles_persona'] ?? null;
        $this->roles_persona_nombre = $args['roles_persona_nombre'] ?? '';
        $this->roles_persona_descripcion = $args['roles_persona_descripcion'] ?? '';
        $this->roles_persona_situacion = $args['roles_persona_situacion'] ?? 1;
    }
}
