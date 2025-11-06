<?php
// models/UsuariosPermisos.php
namespace Model;

class UsuariosPermisos extends ActiveRecord
{
    protected static $tabla = 'usuarios_permisos';
    public static $idTabla = 'id_usuarios_permisos';
    protected static $columnasDB = [
        'usuarios_id_usuarios',
        'modulos_id_modulos',
        'puede_ver',
        'puede_crear',
        'puede_editar',
        'puede_eliminar',
        'puede_exportar_excel',
        'puede_exportar_pdf'
    ];

    public $id_usuarios_permisos;
    public $usuarios_id_usuarios;
    public $modulos_id_modulos;
    public $puede_ver;
    public $puede_crear;
    public $puede_editar;
    public $puede_eliminar;
    public $puede_exportar_excel;
    public $puede_exportar_pdf;

    public function __construct($args = [])
    {
        $this->id_usuarios_permisos = $args['id_usuarios_permisos'] ?? null;
        $this->usuarios_id_usuarios = $args['usuarios_id_usuarios'] ?? null;
        $this->modulos_id_modulos = $args['modulos_id_modulos'] ?? null;
        $this->puede_ver = $args['puede_ver'] ?? 0;
        $this->puede_crear = $args['puede_crear'] ?? 0;
        $this->puede_editar = $args['puede_editar'] ?? 0;
        $this->puede_eliminar = $args['puede_eliminar'] ?? 0;
        $this->puede_exportar_excel = $args['puede_exportar_excel'] ?? 0;
        $this->puede_exportar_pdf = $args['puede_exportar_pdf'] ?? 0;
    }

    public function validar()
    {
        parent::validar();

        if (!$this->usuarios_id_usuarios) {
            self::setAlerta('error', 'El usuario es obligatorio');
        }

        if (!$this->modulos_id_modulos) {
            self::setAlerta('error', 'El módulo es obligatorio');
        }

        return self::getAlertas();
    }
}
