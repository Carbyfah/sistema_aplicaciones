<?php
// controllers/ModulosController.php

namespace Controllers;

use Exception;
use Model\Modulos;
use MVC\Router;

class ModulosController
{
    public static function index(Router $router)
    {
        $router->render('api/modulos', [
            'titulo' => 'Gestión de Módulos'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;

            $query = "SELECT 
                        m.*,
                        mp.modulos_nombre as padre_nombre
                      FROM modulos m
                      LEFT JOIN modulos mp ON m.modulo_padre_id = mp.id_modulos
                      WHERE m.modulos_situacion = {$situacion}
                      ORDER BY m.modulo_padre_id, m.modulos_nombre";

            $modulos = Modulos::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $modulos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener módulos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerJerarquiaAPI()
    {
        getHeadersApi();

        try {
            $query = "SELECT * FROM modulos WHERE modulos_situacion = 1 ORDER BY modulo_padre_id, modulos_nombre";
            $modulos = Modulos::fetchArray($query);

            $jerarquia = [];
            foreach ($modulos as $modulo) {
                if ($modulo['modulo_padre_id'] == null) {
                    $modulo['hijos'] = [];
                    $jerarquia[$modulo['id_modulos']] = $modulo;
                }
            }

            foreach ($modulos as $modulo) {
                if ($modulo['modulo_padre_id'] != null) {
                    if (isset($jerarquia[$modulo['modulo_padre_id']])) {
                        $jerarquia[$modulo['modulo_padre_id']]['hijos'][] = $modulo;
                    }
                }
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => array_values($jerarquia)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener jerarquía',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['modulos_nombre'];

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
            $id = $datos['id_modulos'] ?? null;

            if ($id) {
                $modulo = Modulos::find($id);
                if (!$modulo) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Módulo no encontrado'
                    ]);
                    return;
                }
            } else {
                $modulo = new Modulos();
            }

            $modulo->sincronizar($datos);
            $resultado = $modulo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Módulo guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el módulo'
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
            $modulo = Modulos::find($datos['id']);

            if (!$modulo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Módulo no encontrado'
                ]);
                return;
            }

            $modulo->modulos_situacion = 0;
            $resultado = $modulo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Módulo eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el módulo'
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
            $modulo = Modulos::find($datos['id']);

            if (!$modulo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Módulo no encontrado'
                ]);
                return;
            }

            $modulo->modulos_situacion = 1;
            $resultado = $modulo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Módulo recuperado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el módulo'
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
