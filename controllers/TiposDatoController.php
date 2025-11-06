<?php

namespace Controllers;

use Exception;
use Model\TiposDato;
use MVC\Router;

class TiposDatoController
{
    public static function index(Router $router)
    {
        $router->render('api/tipos_dato', [
            'titulo' => 'Gestión de Tipos de Dato'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $tipos = TiposDato::where('tipos_dato_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $tipos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener tipos de dato',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['tipos_dato_nombre'];

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
            $id = $datos['id_tipo_dato'] ?? null;

            if ($id) {
                $tipo = TiposDato::find($id);
                if (!$tipo) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Tipo de dato no encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
            } else {
                $tipo = new TiposDato();
            }

            $tipo->sincronizar($datos);
            $alertas = $tipo->validar();

            if (!empty($alertas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Errores de validación',
                    'alertas' => $alertas
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $tipo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tipo de dato guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el tipo de dato'
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
            $tipo = TiposDato::find($datos['id']);

            if (!$tipo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tipo de dato no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $tipo->tipos_dato_situacion = 0;
            $resultado = $tipo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tipo de dato eliminado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el tipo de dato'
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
            $tipo = TiposDato::find($datos['id']);

            if (!$tipo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tipo de dato no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $tipo->tipos_dato_situacion = 1;
            $resultado = $tipo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tipo de dato recuperado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el tipo de dato'
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
