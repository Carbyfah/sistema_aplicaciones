<?php

namespace Controllers;

use Exception;
use Model\AplicacionCampos;
use Model\AplicacionTablas;
use Model\TiposDato;
use Model\TiposClave;
use MVC\Router;

class AplicacionCamposController
{
    public static function index(Router $router)
    {
        $tablas = AplicacionTablas::where('tablas_situacion', 1);
        $tipos_dato = TiposDato::where('tipos_dato_situacion', 1);
        $tipos_clave = TiposClave::where('tipos_clave_situacion', 1);

        $router->render('api/aplicacion_campos', [
            'titulo' => 'Gestión de Campos',
            'tablas' => $tablas,
            'tipos_dato' => $tipos_dato,
            'tipos_clave' => $tipos_clave
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
                    at.tablas_nombre,
                    td.tipos_dato_nombre,
                    tc.tipos_clave_nombre
                FROM aplicacion_campos ac
                LEFT JOIN aplicacion_tablas at ON ac.aplicacion_tablas_id = at.id_aplicacion_tablas
                LEFT JOIN tipos_dato td ON ac.tipo_dato_id = td.id_tipo_dato
                LEFT JOIN tipos_clave tc ON ac.tipo_clave_id = tc.id_tipo_clave
                WHERE ac.campos_situacion = {$situacion}
                ORDER BY at.tablas_nombre, ac.campos_nombre
            ";

            $campos = AplicacionCampos::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $campos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener campos',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function obtenerPorTablaAPI()
    {
        getHeadersApi();

        try {
            $tabla_id = $_GET['tabla_id'] ?? null;

            if (!$tabla_id) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de tabla no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $query = "
                SELECT 
                    ac.*,
                    at.tablas_nombre,
                    td.tipos_dato_nombre,
                    tc.tipos_clave_nombre
                FROM aplicacion_campos ac
                LEFT JOIN aplicacion_tablas at ON ac.aplicacion_tablas_id = at.id_aplicacion_tablas
                LEFT JOIN tipos_dato td ON ac.tipo_dato_id = td.id_tipo_dato
                LEFT JOIN tipos_clave tc ON ac.tipo_clave_id = tc.id_tipo_clave
                WHERE ac.aplicacion_tablas_id = {$tabla_id}
                AND ac.campos_situacion = 1
                ORDER BY ac.campos_nombre
            ";

            $campos = AplicacionCampos::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $campos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener campos',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['aplicacion_tablas_id', 'campos_nombre', 'tipo_dato_id'];

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
            $id = $datos['id_aplicacion_campos'] ?? null;

            if ($id) {
                $campo = AplicacionCampos::find($id);
                if (!$campo) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Campo no encontrado'
                    ], JSON_UNESCAPED_UNICODE);
                    return;
                }
                $datos['modificado_por'] = $_SESSION['id'] ?? null;
            } else {
                $campo = new AplicacionCampos();
                $datos['creado_por'] = $_SESSION['id'] ?? null;
            }

            $campo->sincronizar($datos);
            $alertas = $campo->validar();

            if (!empty($alertas)) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Errores de validación',
                    'alertas' => $alertas
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $resultado = $campo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Campo guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el campo'
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
            $campo = AplicacionCampos::find($datos['id']);

            if (!$campo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Campo no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $campo->campos_situacion = 0;
            $resultado = $campo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Campo eliminado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el campo'
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
            $campo = AplicacionCampos::find($datos['id']);

            if (!$campo) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Campo no encontrado'
                ], JSON_UNESCAPED_UNICODE);
                return;
            }

            $campo->campos_situacion = 1;
            $resultado = $campo->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Campo recuperado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el campo'
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
