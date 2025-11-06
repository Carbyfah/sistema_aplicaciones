<?php

namespace Controllers;

use Exception;
use Model\TareasAplicaciones;
use MVC\Router;

class TareasAplicacionesController
{
    public static function index(Router $router)
    {
        $router->render('api/tareas_aplicaciones', [
            'titulo' => 'Gestión de Tareas'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $proyecto_id = $_GET['proyecto_id'] ?? null;

            // Solución simple y efectiva para el parámetro situacion
            if (strpos($situacion, ',') !== false) {
                // Si hay una coma, usar IN para múltiples valores
                $query = "SELECT * FROM tareas_aplicaciones WHERE tareas_aplicaciones_situacion IN (" . $situacion . ")";
            } else {
                // Si es un solo valor, usar igualdad simple
                $query = "SELECT * FROM tareas_aplicaciones WHERE tareas_aplicaciones_situacion = " . intval($situacion) . " ";
            }

            if ($proyecto_id) {
                $query .= " AND ordenes_aplicaciones_id_ordenes_aplicaciones = " . intval($proyecto_id);
            }

            $tareas = TareasAplicaciones::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $tareas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener tareas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['tareas_aplicaciones_titulo', 'ordenes_aplicaciones_id_ordenes_aplicaciones'];

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
            if (session_status() === PHP_SESSION_NONE) {
                session_start();
            }

            // CRÍTICO: Convertir string vacío a null
            $id = $datos['id_tareas_aplicaciones'] ?? null;
            if ($id === '' || $id === 0 || $id === '0') {
                $id = null;
            }

            if ($id) {
                $tarea = TareasAplicaciones::find($id);
                if (!$tarea) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Tarea no encontrada'
                    ]);
                    return;
                }
            } else {
                $tarea = new TareasAplicaciones();
                $datos['usuarios_id_usuarios'] = $_SESSION['usuario_id'] ?? 1;
                $datos['tareas_aplicaciones_completada'] = 0;
                $datos['tareas_aplicaciones_situacion'] = 1;
                $datos['tareas_aplicaciones_prioridad'] = 'Media';

                // CRÍTICO: Eliminar el id_tareas_aplicaciones de los datos
                unset($datos['id_tareas_aplicaciones']);
            }

            $tarea->sincronizar($datos);
            $resultado = $tarea->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tarea guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la tarea'
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
            $tarea = TareasAplicaciones::find($datos['id']);

            if (!$tarea) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tarea no encontrada'
                ]);
                return;
            }

            $tarea->tareas_aplicaciones_situacion = 0;
            $resultado = $tarea->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tarea eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la tarea'
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

    public static function recuperarAPI()
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
            $tarea = TareasAplicaciones::find($datos['id']);

            if (!$tarea) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tarea no encontrada'
                ]);
                return;
            }

            $tarea->tareas_aplicaciones_situacion = 1;
            $resultado = $tarea->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tarea recuperada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la tarea'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al recuperar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function marcarCompletadaAPI()
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
            $tarea = TareasAplicaciones::find($datos['id']);

            if (!$tarea) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tarea no encontrada'
                ]);
                return;
            }

            // Verificar si ya está completada
            if ($tarea->tareas_aplicaciones_completada == 1) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'La tarea ya está completada'
                ]);
                return;
            }

            $tarea->tareas_aplicaciones_completada = 1;
            $tarea->tareas_aplicaciones_fecha_completada = date('Y-m-d H:i:s');
            $resultado = $tarea->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tarea marcada como completada'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al marcar la tarea'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al marcar la tarea',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function reabrirAPI()
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
            $tarea = TareasAplicaciones::find($datos['id']);

            if (!$tarea) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tarea no encontrada'
                ]);
                return;
            }

            // Verificar si ya está reabierta
            if ($tarea->tareas_aplicaciones_completada == 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'La tarea ya está reabierta'
                ]);
                return;
            }

            $tarea->tareas_aplicaciones_completada = 0;
            $tarea->tareas_aplicaciones_fecha_completada = null;
            $resultado = $tarea->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tarea reabierta correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al reabrir la tarea'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al reabrir la tarea',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    /**
     * Obtiene el progreso de un proyecto basado en el porcentaje de tareas completadas
     */
    public static function obtenerProgresoAPI()
    {
        getHeadersApi();

        if (!isset($_GET['proyecto_id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID del proyecto no proporcionado'
            ]);
            return;
        }

        $proyectoId = intval($_GET['proyecto_id']);

        try {
            // Obtener total de tareas activas
            $queryTotal = "SELECT COUNT(*) as total FROM tareas_aplicaciones 
                      WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$proyectoId} 
                      AND tareas_aplicaciones_situacion = 1";

            $resultadoTotal = TareasAplicaciones::fetchFirst($queryTotal);
            $totalTareas = $resultadoTotal['total'] ?? 0;

            // Si no hay tareas, el progreso es 0%
            if ($totalTareas == 0) {
                echo json_encode([
                    'exito' => true,
                    'data' => [
                        'progreso' => 0,
                        'total_tareas' => 0,
                        'completadas' => 0
                    ]
                ]);
                return;
            }

            // Obtener tareas completadas
            $queryCompletadas = "SELECT COUNT(*) as completadas FROM tareas_aplicaciones 
                           WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$proyectoId} 
                           AND tareas_aplicaciones_situacion = 1 
                           AND tareas_aplicaciones_completada = 1";

            $resultadoCompletadas = TareasAplicaciones::fetchFirst($queryCompletadas);
            $tareasCompletadas = $resultadoCompletadas['completadas'] ?? 0;

            // Calcular porcentaje
            $porcentaje = ($totalTareas > 0) ? round(($tareasCompletadas / $totalTareas) * 100) : 0;

            echo json_encode([
                'exito' => true,
                'data' => [
                    'progreso' => $porcentaje,
                    'total_tareas' => $totalTareas,
                    'completadas' => $tareasCompletadas
                ]
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al calcular el progreso',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
