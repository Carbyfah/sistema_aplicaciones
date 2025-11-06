<?php

namespace Controllers;

use Exception;
use Model\Documentos;
use MVC\Router;

class DocumentosController
{
    public static function index(Router $router)
    {
        $router->render('api/documentos', [
            'titulo' => 'Gestión de Documentos'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $proyecto_id = $_GET['proyecto_id'] ?? null;
            $categoria_id = $_GET['categoria_id'] ?? null;

            $query = "SELECT d.*, c.categorias_documentos_nombre as categoria_nombre, 
                     u.usuarios_nombre, 
                     p.persona_nombres, p.persona_apellidos, 
                     oa.ordenes_aplicaciones_codigo, a.aplicacion_nombre as proyecto_nombre 
                     FROM documentos d
                     JOIN categorias_documentos c ON d.categorias_documentos_id_categorias_documentos = c.id_categorias_documentos
                     JOIN usuarios u ON d.usuarios_id_usuarios = u.id_usuarios
                     JOIN persona p ON u.persona_id_persona = p.id_persona
                     JOIN ordenes_aplicaciones oa ON d.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
                     JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
                     WHERE d.documentos_situacion = " . intval($situacion);

            if ($proyecto_id) {
                $query .= " AND d.ordenes_aplicaciones_id_ordenes_aplicaciones = " . intval($proyecto_id);
            }

            if ($categoria_id) {
                $query .= " AND d.categorias_documentos_id_categorias_documentos = " . intval($categoria_id);
            }

            // $query .= " AND d.documento_original_id IS NULL";

            $documentos = Documentos::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $documentos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener documentos',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function versionesAPI()
    {
        getHeadersApi();

        try {
            $documento_id = $_GET['documento_id'] ?? null;

            if (!$documento_id) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'ID de documento no proporcionado'
                ]);
                return;
            }

            $query = "SELECT d.*, u.usuarios_nombre, p.persona_nombres, p.persona_apellidos
                     FROM documentos d
                     JOIN usuarios u ON d.usuarios_id_usuarios = u.id_usuarios
                     JOIN persona p ON u.persona_id_persona = p.id_persona
                     WHERE d.documento_original_id = " . intval($documento_id) .
                " AND d.documentos_situacion = 1
                     ORDER BY d.documentos_version DESC";

            $versiones = Documentos::fetchArray($query);

            $documento_original = Documentos::find($documento_id);

            if ($documento_original) {
                $query = "SELECT u.usuarios_nombre, p.persona_nombres, p.persona_apellidos
                         FROM usuarios u 
                         JOIN persona p ON u.persona_id_persona = p.id_persona
                         WHERE u.id_usuarios = " . intval($documento_original->usuarios_id_usuarios);

                $usuario = Documentos::fetchFirst($query);

                $original_data = [
                    'id_documentos' => $documento_original->id_documentos,
                    'documentos_nombre' => $documento_original->documentos_nombre,
                    'documentos_ruta' => $documento_original->documentos_ruta,
                    'documentos_tamanio' => $documento_original->documentos_tamanio,
                    'documentos_extension' => $documento_original->documentos_extension,
                    'documentos_version' => $documento_original->documentos_version,
                    'documentos_fecha_subida' => $documento_original->documentos_fecha_subida,
                    'usuarios_nombre' => $usuario['usuarios_nombre'] ?? '',
                    'persona_nombres' => $usuario['persona_nombres'] ?? '',
                    'persona_apellidos' => $usuario['persona_apellidos'] ?? ''
                ];

                array_unshift($versiones, $original_data);
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $versiones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener versiones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = [
            'documentos_nombre',
            'documentos_ruta',
            'ordenes_aplicaciones_id_ordenes_aplicaciones',
            'categorias_documentos_id_categorias_documentos',
            'usuarios_id_usuarios'
        ];

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
            $documento = new Documentos();

            if (isset($datos['es_version']) && $datos['es_version'] && isset($datos['documento_original_id'])) {
                $original = Documentos::find($datos['documento_original_id']);

                if ($original) {
                    $documento->documento_original_id = $original->id_documentos;

                    $query = "SELECT MAX(documentos_version) as ultima_version 
                             FROM documentos 
                             WHERE documento_original_id = " . intval($original->id_documentos) .
                        " OR id_documentos = " . intval($original->id_documentos);

                    $result = Documentos::fetchFirst($query);

                    $documento->documentos_version = ($result['ultima_version'] ?? 0) + 1;
                }
            } else {
                $query = "SELECT * FROM documentos 
                         WHERE documentos_nombre = " . Documentos::quote($datos['documentos_nombre']) .
                    " AND ordenes_aplicaciones_id_ordenes_aplicaciones = " . intval($datos['ordenes_aplicaciones_id_ordenes_aplicaciones']) .
                    " AND documento_original_id IS NULL
                         AND documentos_situacion = 1";

                $existente = Documentos::fetchFirst($query);

                if ($existente) {
                    $documento->documento_original_id = $existente['id_documentos'];
                    $documento->documentos_version = $existente['documentos_version'] + 1;
                }
            }

            $documento->documentos_fecha_subida = date('Y-m-d H:i:s');

            $documento->sincronizar($datos);
            $resultado = $documento->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Documento guardado correctamente',
                    'id' => $resultado['id'],
                    'es_nueva_version' => isset($documento->documento_original_id) && $documento->documento_original_id !== null,
                    'version' => $documento->documentos_version
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el documento'
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
            $documento = Documentos::find($datos['id']);

            if (!$documento) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Documento no encontrado'
                ]);
                return;
            }

            $documento->documentos_situacion = 0;
            $resultado = $documento->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Documento eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el documento'
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
            $documento = Documentos::find($datos['id']);

            if (!$documento) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Documento no encontrado'
                ]);
                return;
            }

            $documento->documentos_situacion = 1;
            $resultado = $documento->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Documento recuperado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el documento'
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
