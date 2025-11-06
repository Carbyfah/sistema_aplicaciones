<?php

namespace Controllers;

use Exception;
use Model\Aplicacion;
use MVC\Router;

class AplicacionController
{
    public static function index(Router $router)
    {
        $router->render('api/aplicacion', [
            'titulo' => 'Gestión de Proyectos'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $aplicaciones = Aplicacion::where('aplicacion_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $aplicaciones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener proyectos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['aplicacion_nombre'];

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
            $id = $datos['id_aplicacion'] ?? null;

            if ($id) {
                $aplicacion = Aplicacion::find($id);
                if (!$aplicacion) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Proyecto no encontrado'
                    ]);
                    return;
                }
                $aplicacion->fecha_modificacion = date('Y-m-d H:i:s');
                $aplicacion->modificado_por = $_SESSION['usuario_id'] ?? null;
            } else {
                $aplicacion = new Aplicacion();
                $aplicacion->fecha_creacion = date('Y-m-d H:i:s');
                $aplicacion->creado_por = $_SESSION['usuario_id'] ?? null;
            }

            $aplicacion->sincronizar($datos);
            $resultado = $aplicacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el proyecto'
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
            $aplicacion = Aplicacion::find($datos['id']);

            if (!$aplicacion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado'
                ]);
                return;
            }

            $aplicacion->aplicacion_situacion = 0;
            $aplicacion->fecha_modificacion = date('Y-m-d H:i:s');
            $aplicacion->modificado_por = $_SESSION['usuario_id'] ?? null;
            $resultado = $aplicacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el proyecto'
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
            $aplicacion = Aplicacion::find($datos['id']);

            if (!$aplicacion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado'
                ]);
                return;
            }

            $aplicacion->aplicacion_situacion = 1;
            $aplicacion->fecha_modificacion = date('Y-m-d H:i:s');
            $aplicacion->modificado_por = $_SESSION['usuario_id'] ?? null;
            $resultado = $aplicacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto recuperado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el proyecto'
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
