<?php

namespace Controllers;

use Exception;
use Model\Notificaciones;
use MVC\Router;

class NotificacionesController
{
    public static function index(Router $router)
    {
        $router->render('api/notificaciones', [
            'titulo' => 'Notificaciones'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $leidas = $_GET['leidas'] ?? null;
            $persona_id = $_SESSION['persona_id'] ?? null;

            if (!$persona_id) {
                http_response_code(401);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario no autenticado'
                ]);
                return;
            }

            $notificaciones = Notificaciones::where('persona_id_persona', $persona_id, ' = ');

            // Filtrar por situación
            $notificaciones = array_filter($notificaciones, function ($notificacion) use ($situacion) {
                return $notificacion->notificaciones_situacion == $situacion;
            });

            // Filtrar por leídas/no leídas si se especifica
            if ($leidas !== null) {
                $notificaciones = array_filter($notificaciones, function ($notificacion) use ($leidas) {
                    return $notificacion->notificaciones_leida == $leidas;
                });
            }

            // Ordenar por fecha más reciente
            usort($notificaciones, function ($a, $b) {
                return strtotime($b->notificaciones_fecha) - strtotime($a->notificaciones_fecha);
            });

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => array_values($notificaciones)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener notificaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['notificaciones_titulo', 'notificaciones_mensaje', 'persona_id_persona'];

        foreach ($campos as $campo) {
            if (!isset($datos[$campo]) || trim($datos[$campo]) === '') {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => "El campo $campo es requerido"
                ]);
                return;
            }
        }

        try {
            $id = $datos['id_notificaciones'] ?? null;

            if ($id) {
                $notificacion = Notificaciones::find($id);
                if (!$notificacion) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Notificación no encontrada'
                    ]);
                    return;
                }
            } else {
                $notificacion = new Notificaciones();
                $notificacion->notificaciones_fecha = date('Y-m-d H:i:s');
                $notificacion->notificaciones_leida = 0;
                $notificacion->notificaciones_tipo = $datos['notificaciones_tipo'] ?? 'INFO';
            }

            $notificacion->sincronizar($datos);
            $resultado = $notificacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Notificación guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la notificación'
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

    public static function eliminarAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID no proporcionado'
            ]);
            return;
        }

        try {
            $notificacion = Notificaciones::find($datos['id']);

            if (!$notificacion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Notificación no encontrada'
                ]);
                return;
            }

            $notificacion->notificaciones_situacion = 0;
            $resultado = $notificacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Notificación eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la notificación'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function marcarLeidaAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID no proporcionado'
            ]);
            return;
        }

        try {
            $notificacion = Notificaciones::find($datos['id']);

            if (!$notificacion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Notificación no encontrada'
                ]);
                return;
            }

            $notificacion->notificaciones_leida = 1;
            $resultado = $notificacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Notificación marcada como leída'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al actualizar la notificación'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al marcar como leída',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function marcarTodasLeidasAPI()
    {
        getHeadersApi();

        try {
            $persona_id = $_SESSION['persona_id'] ?? null;

            if (!$persona_id) {
                http_response_code(401);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario no autenticado'
                ]);
                return;
            }

            // Obtener todas las notificaciones no leídas del usuario
            $notificaciones = Notificaciones::where('persona_id_persona', $persona_id, ' = ');
            $notificaciones = array_filter($notificaciones, function ($notificacion) {
                return $notificacion->notificaciones_leida == 0 && $notificacion->notificaciones_situacion == 1;
            });

            $actualizadas = 0;

            foreach ($notificaciones as $notificacion) {
                $notificacion->notificaciones_leida = 1;
                $resultado = $notificacion->guardar();
                if ($resultado['resultado'] > 0) {
                    $actualizadas++;
                }
            }

            echo json_encode([
                'exito' => true,
                'mensaje' => "Se marcaron {$actualizadas} notificaciones como leídas",
                'total' => $actualizadas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al marcar todas como leídas',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
