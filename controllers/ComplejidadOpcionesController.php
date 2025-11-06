<?php

namespace Controllers;

use Exception;
use Model\ComplejidadOpciones;
use MVC\Router;

class ComplejidadOpcionesController
{
    public static function index(Router $router)
    {
        $router->render('api/complejidad_opciones', [
            'titulo' => 'Gestión de Complejidad'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $complejidades = ComplejidadOpciones::where('complejidad_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $complejidades
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener complejidades',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['complejidad_nombre', 'complejidad_factor'];

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
            $id = $datos['id_complejidad'] ?? null;

            if ($id) {
                $complejidad = ComplejidadOpciones::find($id);
                if (!$complejidad) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Complejidad no encontrada'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
            } else {
                $complejidad = new ComplejidadOpciones();
            }

            $complejidad->sincronizar($datos);
            $alertas = $complejidad->validar();

            if (!empty($alertas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Errores de validación',
                    'alertas' => $alertas
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $complejidad->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Complejidad guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la complejidad'
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
            $complejidad = ComplejidadOpciones::find($datos['id']);

            if (!$complejidad) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Complejidad no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $complejidad->complejidad_situacion = 0;
            $resultado = $complejidad->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Complejidad eliminada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la complejidad'
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
            $complejidad = ComplejidadOpciones::find($datos['id']);

            if (!$complejidad) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Complejidad no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $complejidad->complejidad_situacion = 1;
            $resultado = $complejidad->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Complejidad recuperada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la complejidad'
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
