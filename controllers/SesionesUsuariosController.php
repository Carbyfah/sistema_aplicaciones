<?php

namespace Controllers;

use Exception;
use Model\SesionesUsuarios;
use MVC\Router;

class SesionesUsuariosController
{
    public static function index(Router $router)
    {
        $router->render('api/sesiones-usuarios', [
            'titulo' => 'Registro de Sesiones'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $estado = isset($_GET['estado']) ? intval($_GET['estado']) : null;
            $usuario_id = isset($_GET['usuario_id']) ? intval($_GET['usuario_id']) : null;
            $fecha = $_GET['fecha'] ?? null;

            $query = "SELECT su.*, u.usuarios_nombre, p.persona_nombres, p.persona_apellidos 
                 FROM sesiones_usuarios su
                 JOIN usuarios u ON su.usuarios_id_usuarios = u.id_usuarios
                 JOIN persona p ON u.persona_id_persona = p.id_persona";

            $condiciones = [];

            if ($estado !== null) {
                $condiciones[] = "su.sesion_estado = {$estado}";
            }

            if ($usuario_id) {
                $condiciones[] = "su.usuarios_id_usuarios = {$usuario_id}";
            }

            if ($fecha) {
                $condiciones[] = "DATE(su.sesion_fecha_inicio) = '{$fecha}'";
            }

            if (!empty($condiciones)) {
                $query .= " WHERE " . implode(" AND ", $condiciones);
            }

            $query .= " ORDER BY su.sesion_fecha_inicio DESC LIMIT 100";

            $sesiones = SesionesUsuarios::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $sesiones
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener sesiones',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function iniciarSesionAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['usuario_id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID de usuario no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $sesion = new SesionesUsuarios();
            $sesion->usuarios_id_usuarios = $datos['usuario_id'];
            $sesion->sesion_token = bin2hex(random_bytes(32));
            $sesion->sesion_fecha_inicio = date('Y-m-d H:i:s');
            $sesion->sesion_estado = 1;

            $resultado = $sesion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Sesión iniciada correctamente',
                    'token' => $sesion->sesion_token,
                    'id' => $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al iniciar sesión'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al iniciar sesión',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function actualizarUltimoAccesoAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['token'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Token no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $sesiones = SesionesUsuarios::where('sesion_token', $datos['token']);

            if (empty($sesiones)) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Sesión no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $sesion = $sesiones[0];

            if ($sesion->sesion_estado == 0) {
                http_response_code(401);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Sesión cerrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $sesion->sesion_fecha_inicio = date('Y-m-d H:i:s');
            $resultado = $sesion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Último acceso actualizado'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al actualizar último acceso'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al actualizar',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function cerrarSesionAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['token'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Token no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $sesiones = SesionesUsuarios::where('sesion_token', $datos['token']);

            if (empty($sesiones)) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Sesión no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $sesion = $sesiones[0];
            $resultado = $sesion->cerrarSesion();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Sesión cerrada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al cerrar sesión'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al cerrar sesión',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
