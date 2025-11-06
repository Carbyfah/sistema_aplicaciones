<?php

namespace Controllers;

use Exception;
use Model\LogsActividad;
use MVC\Router;

class LogsActividadController
{
    public static function index(Router $router)
    {
        $router->render('api/logs-actividad', [
            'titulo' => 'Logs de Actividad'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $limite = $_GET['limite'] ?? 100;

            // CORREGIDO: Usar SQL directamente sin acceder a $db
            $query = "SELECT la.*, p.persona_nombres, p.persona_apellidos 
                      FROM logs_actividad la 
                      LEFT JOIN persona p ON la.persona_id_persona = p.id_persona 
                      WHERE la.logs_actividad_situacion = " . intval($situacion) . "
                      ORDER BY la.logs_actividad_fecha DESC 
                      LIMIT " . intval($limite);

            $logs = LogsActividad::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $logs
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener logs',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['logs_actividad_accion', 'logs_actividad_tabla', 'logs_actividad_registro_id', 'persona_id_persona'];

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
            $log = new LogsActividad();
            $log->logs_actividad_fecha = date('Y-m-d H:i:s');
            $log->logs_actividad_ip = $_SERVER['REMOTE_ADDR'] ?? '';

            $log->sincronizar($datos);
            $resultado = $log->guardar();

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Log guardado correctamente',
                    'id' => $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el log'
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

    public static function registrarActividadAPI()
    {
        $datos = getHeadersApi();

        $campos = ['accion', 'tabla', 'registro_id', 'datos_antiguos', 'datos_nuevos'];

        foreach ($campos as $campo) {
            if (!isset($datos[$campo])) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => "El campo $campo es requerido"
                ]);
                return;
            }
        }

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

            $resultado = LogsActividad::registrar(
                $datos['accion'],
                $datos['tabla'],
                $datos['registro_id'],
                $datos['datos_antiguos'],
                $datos['datos_nuevos'],
                $persona_id
            );

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Actividad registrada correctamente',
                    'id' => $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al registrar actividad'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al registrar actividad',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
