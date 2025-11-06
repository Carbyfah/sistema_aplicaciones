<?php
// controllers/UsuariosPermisosController.php

namespace Controllers;

use Exception;
use Model\UsuariosPermisos;
use Model\Modulos;
use MVC\Router;

class UsuariosPermisosController
{
    public static function index(Router $router)
    {
        $router->render('api/usuarios-permisos', [
            'titulo' => 'Asignar Permisos a Usuarios'
        ]);
    }

    public static function obtenerPermisosUsuarioAPI()
    {
        getHeadersApi();

        try {
            $usuarioId = $_GET['usuario_id'] ?? null;

            if (!$usuarioId) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de usuario no proporcionado'
                ]);
                return;
            }

            $query = "SELECT 
                        up.*,
                        m.modulos_nombre,
                        m.modulos_descripcion,
                        m.modulo_padre_id
                      FROM usuarios_permisos up
                      INNER JOIN modulos m ON up.modulos_id_modulos = m.id_modulos
                      WHERE up.usuarios_id_usuarios = {$usuarioId}
                      ORDER BY m.modulo_padre_id, m.modulos_nombre";

            $permisos = UsuariosPermisos::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $permisos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener permisos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerModulosConPermisosAPI()
    {
        getHeadersApi();

        try {
            $usuarioId = $_GET['usuario_id'] ?? null;

            if (!$usuarioId) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de usuario no proporcionado'
                ]);
                return;
            }

            $queryModulos = "SELECT * FROM modulos WHERE modulos_situacion = 1 ORDER BY modulo_padre_id, modulos_nombre";
            $modulos = Modulos::fetchArray($queryModulos);

            $queryPermisos = "SELECT * FROM usuarios_permisos WHERE usuarios_id_usuarios = {$usuarioId}";
            $permisos = UsuariosPermisos::fetchArray($queryPermisos);

            $permisosMap = [];
            foreach ($permisos as $permiso) {
                $permisosMap[$permiso['modulos_id_modulos']] = $permiso;
            }

            foreach ($modulos as &$modulo) {
                if (isset($permisosMap[$modulo['id_modulos']])) {
                    $modulo['permisos'] = $permisosMap[$modulo['id_modulos']];
                } else {
                    $modulo['permisos'] = [
                        'puede_ver' => 0,
                        'puede_crear' => 0,
                        'puede_editar' => 0,
                        'puede_eliminar' => 0,
                        'puede_exportar_excel' => 0,
                        'puede_exportar_pdf' => 0
                    ];
                }
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $modulos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener módulos con permisos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarPermisosAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['usuario_id']) || !isset($datos['modulo_id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Usuario y módulo son requeridos'
            ]);
            return;
        }

        try {
            $usuarioId = $datos['usuario_id'];
            $moduloId = $datos['modulo_id'];

            $queryExiste = "SELECT * FROM usuarios_permisos 
                           WHERE usuarios_id_usuarios = {$usuarioId} 
                           AND modulos_id_modulos = {$moduloId}";

            $existe = UsuariosPermisos::fetchFirst($queryExiste);

            if ($existe) {
                $permiso = UsuariosPermisos::find($existe['id_usuarios_permisos']);
            } else {
                $permiso = new UsuariosPermisos();
                $permiso->usuarios_id_usuarios = $usuarioId;
                $permiso->modulos_id_modulos = $moduloId;
            }

            $permiso->puede_ver = $datos['puede_ver'] ?? 0;
            $permiso->puede_crear = $datos['puede_crear'] ?? 0;
            $permiso->puede_editar = $datos['puede_editar'] ?? 0;
            $permiso->puede_eliminar = $datos['puede_eliminar'] ?? 0;
            $permiso->puede_exportar_excel = $datos['puede_exportar_excel'] ?? 0;
            $permiso->puede_exportar_pdf = $datos['puede_exportar_pdf'] ?? 0;

            $resultado = $permiso->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Permisos guardados correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar permisos'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarTodosPermisosAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['usuario_id']) || !isset($datos['permisos'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Usuario y permisos son requeridos'
            ]);
            return;
        }

        try {
            $usuarioId = $datos['usuario_id'];
            $permisos = $datos['permisos'];

            $queryDelete = "DELETE FROM usuarios_permisos WHERE usuarios_id_usuarios = {$usuarioId}";
            UsuariosPermisos::SQL($queryDelete);

            foreach ($permisos as $permisoData) {
                if (!isset($permisoData['modulo_id'])) {
                    continue;
                }

                $permiso = new UsuariosPermisos();
                $permiso->usuarios_id_usuarios = $usuarioId;
                $permiso->modulos_id_modulos = $permisoData['modulo_id'];
                $permiso->puede_ver = $permisoData['puede_ver'] ?? 0;
                $permiso->puede_crear = $permisoData['puede_crear'] ?? 0;
                $permiso->puede_editar = $permisoData['puede_editar'] ?? 0;
                $permiso->puede_eliminar = $permisoData['puede_eliminar'] ?? 0;
                $permiso->puede_exportar_excel = $permisoData['puede_exportar_excel'] ?? 0;
                $permiso->puede_exportar_pdf = $permisoData['puede_exportar_pdf'] ?? 0;

                $permiso->guardar();
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Todos los permisos guardados correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al guardar permisos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function eliminarPermisosModuloAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['usuario_id']) || !isset($datos['modulo_id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Usuario y módulo son requeridos'
            ]);
            return;
        }

        try {
            $usuarioId = $datos['usuario_id'];
            $moduloId = $datos['modulo_id'];

            $queryDelete = "DELETE FROM usuarios_permisos 
                           WHERE usuarios_id_usuarios = {$usuarioId} 
                           AND modulos_id_modulos = {$moduloId}";

            UsuariosPermisos::SQL($queryDelete);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Permisos eliminados correctamente'
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al eliminar permisos',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
