<?php

namespace Controllers;

use Exception;
use Model\SeguridadOpciones;
use MVC\Router;

class SeguridadOpcionesController
{
    public static function index(Router $router)
    {
        $router->render('api/seguridad_opciones', [
            'titulo' => 'Gestión de Seguridad'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $seguridades = SeguridadOpciones::where('seguridad_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $seguridades
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener niveles de seguridad',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['seguridad_nombre', 'seguridad_factor'];

        foreach ($campos as $campo) {
            if (!isset($datos[$campo]) || trim($datos[$campo]) === '') {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => "El campo $campo es requerido"
                ], JSON_UNESCAPED_UNICODE);
                return;
            }
        }

        try {
            $id = $datos['id_seguridad'] ?? null;

            if ($id) {
                $seguridad = SeguridadOpciones::find($id);
                if (!$seguridad) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Nivel de seguridad no encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
            } else {
                $seguridad = new SeguridadOpciones();
            }

            $seguridad->sincronizar($datos);
            $alertas = $seguridad->validar();

            if (!empty($alertas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Errores de validación',
                    'alertas' => $alertas
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $seguridad->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Nivel de seguridad guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el nivel de seguridad'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al guardar',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
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
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $seguridad = SeguridadOpciones::find($datos['id']);

            if (!$seguridad) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Nivel de seguridad no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $seguridad->seguridad_situacion = 0;
            $resultado = $seguridad->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Nivel de seguridad eliminado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el nivel de seguridad'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al eliminar',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
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
            ], JSON_UNESCAPED_UNICODE);
            return;
        }

        try {
            $seguridad = SeguridadOpciones::find($datos['id']);

            if (!$seguridad) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Nivel de seguridad no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $seguridad->seguridad_situacion = 1;
            $resultado = $seguridad->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Nivel de seguridad recuperado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el nivel de seguridad'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al recuperar',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}
