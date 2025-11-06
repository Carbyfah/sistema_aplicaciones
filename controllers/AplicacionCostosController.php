<?php

namespace Controllers;

use Exception;
use Model\AplicacionCostos;
use Model\Aplicacion;
use Model\ComplejidadOpciones;
use Model\SeguridadOpciones;
use MVC\Router;

class AplicacionCostosController
{
    public static function index(Router $router)
    {
        $aplicaciones = Aplicacion::where('aplicacion_situacion', 1);
        $complejidades = ComplejidadOpciones::where('complejidad_situacion', 1);
        $seguridades = SeguridadOpciones::where('seguridad_situacion', 1);

        $router->render('api/aplicacion_costos', [
            'titulo' => 'Gestión de Costos',
            'aplicaciones' => $aplicaciones,
            'complejidades' => $complejidades,
            'seguridades' => $seguridades
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;

            $query = "
                SELECT 
                    ac.*,
                    a.aplicacion_nombre,
                    co.complejidad_nombre,
                    co.complejidad_factor,
                    so.seguridad_nombre,
                    so.seguridad_factor
                FROM aplicacion_costos ac
                LEFT JOIN aplicacion a ON ac.aplicacion_id_aplicacion = a.id_aplicacion
                LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
                LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
                WHERE ac.costos_situacion = {$situacion}
                ORDER BY ac.fecha_creacion DESC
            ";

            $costos = AplicacionCostos::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $costos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener costos',
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
                    ac.*,
                    a.aplicacion_nombre,
                    co.complejidad_nombre,
                    co.complejidad_factor,
                    so.seguridad_nombre,
                    so.seguridad_factor
                FROM aplicacion_costos ac
                LEFT JOIN aplicacion a ON ac.aplicacion_id_aplicacion = a.id_aplicacion
                LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
                LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
                WHERE ac.aplicacion_id_aplicacion = {$aplicacion_id}
                AND ac.costos_situacion = 1
                LIMIT 1
            ";

            $costo = AplicacionCostos::fetchFirst($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $costo
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener costo',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['aplicacion_id_aplicacion', 'costos_horas_estimadas', 'costos_tarifa_hora'];

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
            $id = $datos['id_aplicacion_costos'] ?? null;

            if ($id) {
                $costo = AplicacionCostos::find($id);
                if (!$costo) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Costo no encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
                $datos['modificado_por'] = $_SESSION['id'] ?? null;
                $datos['fecha_modificacion'] = date('Y-m-d H:i:s');
            } else {
                $costo = new AplicacionCostos();
                $datos['creado_por'] = $_SESSION['id'] ?? null;
            }

            $costo->sincronizar($datos);
            $alertas = $costo->validar();

            if (!empty($alertas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Errores de validación',
                    'alertas' => $alertas
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            // CALCULAR COSTO TOTAL MANUALMENTE (sin triggers)
            $horas = floatval($costo->costos_horas_estimadas);
            $tarifa = floatval($costo->costos_tarifa_hora);
            $factorComplejidad = 1.0;
            $factorSeguridad = 1.0;

            // Obtener factores si existen
            if ($costo->complejidad_id) {
                $complejidad = ComplejidadOpciones::find($costo->complejidad_id);
                if ($complejidad) {
                    $factorComplejidad = floatval($complejidad->complejidad_factor);
                }
            }

            if ($costo->seguridad_id) {
                $seguridad = SeguridadOpciones::find($costo->seguridad_id);
                if ($seguridad) {
                    $factorSeguridad = floatval($seguridad->seguridad_factor);
                }
            }

            // Calcular costo total
            $costoBase = $horas * $tarifa;
            $costo->costos_total = $costoBase * $factorComplejidad * $factorSeguridad;

            $resultado = $costo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Costo guardado correctamente',
                    'id' => $id ?? $resultado['id'],
                    'costo_total' => $costo->costos_total
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el costo'
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
            $costo = AplicacionCostos::find($datos['id']);

            if (!$costo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Costo no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $costo->costos_situacion = 0;
            $resultado = $costo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Costo eliminado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el costo'
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
            $costo = AplicacionCostos::find($datos['id']);

            if (!$costo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Costo no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $costo->costos_situacion = 1;
            $resultado = $costo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Costo recuperado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el costo'
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
