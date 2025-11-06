<?php

namespace Controllers;

use Exception;
use Model\Estados;
use MVC\Router;

class EstadosController
{
    public static function index(Router $router)
    {
        $router->render('api/estados', [
            'titulo' => 'Gestión de Estados'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $estados = Estados::where('estados_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $estados
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener estados',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['estados_nombre'];

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
            $id = $datos['id_estados'] ?? null;

            if ($id) {
                $estado = Estados::find($id);
                if (!$estado) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Estado no encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
            } else {
                $estado = new Estados();
            }

            $estado->sincronizar($datos);
            $resultado = $estado->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Estado guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el estado'
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
            $estado = Estados::find($datos['id']);

            if (!$estado) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Estado no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $estado->estados_situacion = 0;
            $resultado = $estado->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Estado eliminado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el estado'
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
            $estado = Estados::find($datos['id']);

            if (!$estado) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Estado no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $estado->estados_situacion = 1;
            $resultado = $estado->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Estado recuperado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el estado'
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
