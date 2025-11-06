<?php

namespace Controllers;

use Exception;
use Model\ConfiguracionSistema;
use MVC\Router;

class ConfiguracionSistemaController
{
    public static function index(Router $router)
    {
        $router->render('api/configuracion-sistema', [
            'titulo' => 'Configuración del Sistema'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $configuraciones = ConfiguracionSistema::where('configuracion_sistema_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $configuraciones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener configuraciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['configuracion_sistema_clave', 'configuracion_sistema_valor'];

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
            $id = $datos['id_configuracion_sistema'] ?? null;

            if ($id) {
                $configuracion = ConfiguracionSistema::find($id);
                if (!$configuracion) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Configuración no encontrada'
                    ]);
                    return;
                }
            } else {
                $configuracion = new ConfiguracionSistema();
            }

            $configuracion->sincronizar($datos);
            $resultado = $configuracion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Configuración guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la configuración'
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
            $configuracion = ConfiguracionSistema::find($datos['id']);

            if (!$configuracion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Configuración no encontrada'
                ]);
                return;
            }

            $configuracion->configuracion_sistema_situacion = 0;
            $resultado = $configuracion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Configuración eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la configuración'
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
            $configuracion = ConfiguracionSistema::find($datos['id']);

            if (!$configuracion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Configuración no encontrada'
                ]);
                return;
            }

            $configuracion->configuracion_sistema_situacion = 1;
            $resultado = $configuracion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Configuración recuperada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la configuración'
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
}
