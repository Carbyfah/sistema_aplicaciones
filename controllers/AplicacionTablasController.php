<?php

namespace Controllers;

use Exception;
use Model\AplicacionTablas;
use Model\Aplicacion;
use Model\TiposTabla;
use MVC\Router;

class AplicacionTablasController
{
    public static function index(Router $router)
    {
        $aplicaciones = Aplicacion::where('aplicacion_situacion', 1);
        $tipos_tabla = TiposTabla::where('tipos_tabla_situacion', 1);

        $router->render('api/aplicacion_tablas', [
            'titulo' => 'Gestión de Tablas',
            'aplicaciones' => $aplicaciones,
            'tipos_tabla' => $tipos_tabla
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;

            $query = "
                SELECT 
                    at.*,
                    a.aplicacion_nombre,
                    tt.tipos_tabla_nombre
                FROM aplicacion_tablas at
                LEFT JOIN aplicacion a ON at.aplicacion_id_aplicacion = a.id_aplicacion
                LEFT JOIN tipos_tabla tt ON at.tipo_tabla_id = tt.id_tipo_tabla
                WHERE at.tablas_situacion = {$situacion}
                ORDER BY at.fecha_creacion DESC
            ";

            $tablas = AplicacionTablas::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $tablas
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener tablas',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function obtenerPorAplicacionAPI()
    {
        getHeadersApi();

        try {
            $aplicacion_id = $_GET['aplicacion_id'] ?? null;

            if (!$aplicacion_id) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de aplicación no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $query = "
                SELECT 
                    at.*,
                    a.aplicacion_nombre,
                    tt.tipos_tabla_nombre,
                    (SELECT COUNT(*) FROM aplicacion_campos ac 
                     WHERE ac.aplicacion_tablas_id = at.id_aplicacion_tablas 
                     AND ac.campos_situacion = 1) as total_campos
                FROM aplicacion_tablas at
                LEFT JOIN aplicacion a ON at.aplicacion_id_aplicacion = a.id_aplicacion
                LEFT JOIN tipos_tabla tt ON at.tipo_tabla_id = tt.id_tipo_tabla
                WHERE at.aplicacion_id_aplicacion = {$aplicacion_id}
                AND at.tablas_situacion = 1
                ORDER BY at.tablas_nombre
            ";

            $tablas = AplicacionTablas::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $tablas
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener tablas',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['aplicacion_id_aplicacion', 'tablas_nombre'];

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
            $id = $datos['id_aplicacion_tablas'] ?? null;

            if ($id) {
                $tabla = AplicacionTablas::find($id);
                if (!$tabla) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Tabla no encontrada'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
                $datos['modificado_por'] = $_SESSION['id'] ?? null;
            } else {
                $tabla = new AplicacionTablas();
                $datos['creado_por'] = $_SESSION['id'] ?? null;
            }

            $tabla->sincronizar($datos);
            $alertas = $tabla->validar();

            if (!empty($alertas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Errores de validación',
                    'alertas' => $alertas
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $tabla->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tabla guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la tabla'
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
            $tabla = AplicacionTablas::find($datos['id']);

            if (!$tabla) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tabla no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $tabla->tablas_situacion = 0;
            $resultado = $tabla->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tabla eliminada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la tabla'
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
            $tabla = AplicacionTablas::find($datos['id']);

            if (!$tabla) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Tabla no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $tabla->tablas_situacion = 1;
            $resultado = $tabla->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Tabla recuperada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la tabla'
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
