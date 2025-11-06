<?php

namespace Controllers;

use Exception;
use Model\ContenidoDocumentos;
use Model\Documentos;
use MVC\Router;

class ContenidoDocumentosController
{
    public static function obtenerAPI()
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

            $contenido = ContenidoDocumentos::getContenidoDocumento($documento_id);

            if (empty($contenido)) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'No hay contenido indexado para este documento'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $contenido[0] ?? null
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener contenido',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['documentos_id_documentos']) || !isset($datos['contenido_documentos_texto'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Campos requeridos no proporcionados'
            ]);
            return;
        }

        try {
            $documento = Documentos::find($datos['documentos_id_documentos']);

            if (!$documento) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Documento no encontrado'
                ]);
                return;
            }

            // Verificar si ya existe contenido para este documento
            $contenidoExistente = ContenidoDocumentos::getContenidoDocumento($datos['documentos_id_documentos']);

            if (!empty($contenidoExistente)) {
                // Actualizar el contenido existente
                $contenido = new ContenidoDocumentos();
                $contenido->id_contenido_documentos = $contenidoExistente[0]->id_contenido_documentos;
                $contenido->contenido_documentos_texto = $datos['contenido_documentos_texto'];
                $contenido->documentos_id_documentos = $datos['documentos_id_documentos'];

                $resultado = $contenido->guardar();

                $mensaje = 'Contenido actualizado correctamente';
            } else {
                // Crear nuevo contenido
                $contenido = new ContenidoDocumentos();
                $contenido->contenido_documentos_texto = $datos['contenido_documentos_texto'];
                $contenido->documentos_id_documentos = $datos['documentos_id_documentos'];

                $resultado = $contenido->guardar();

                $mensaje = 'Contenido guardado correctamente';
            }

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => $mensaje,
                    'id' => $resultado['id'] ?? ($contenidoExistente[0]->id_contenido_documentos ?? null)
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el contenido'
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

    public static function buscarAPI()
    {
        getHeadersApi();

        try {
            $texto = $_GET['texto'] ?? null;

            if (!$texto || strlen(trim($texto)) < 3) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Texto de búsqueda demasiado corto, mínimo 3 caracteres'
                ]);
                return;
            }

            $resultados = ContenidoDocumentos::buscarTexto($texto);

            if (empty($resultados)) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'No se encontraron resultados'
                ]);
                return;
            }

            // Obtener información adicional de los documentos
            $documentosInfo = [];
            foreach ($resultados as $resultado) {
                $documento = Documentos::find($resultado->documentos_id_documentos);

                if ($documento) {
                    $query = "SELECT c.categorias_documentos_nombre, oa.ordenes_aplicaciones_codigo,
                             a.aplicacion_nombre, a.id_aplicacion, u.usuarios_nombre,
                             p.persona_nombres, p.persona_apellidos
                             FROM documentos d
                             JOIN categorias_documentos c ON d.categorias_documentos_id_categorias_documentos = c.id_categorias_documentos
                             JOIN ordenes_aplicaciones oa ON d.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
                             JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
                             JOIN usuarios u ON d.usuarios_id_usuarios = u.id_usuarios
                             JOIN persona p ON u.persona_id_persona = p.id_persona
                             WHERE d.id_documentos = " . ContenidoDocumentos::$db->quote($documento->id_documentos);

                    $infoDocumento = Documentos::fetchFirst($query);

                    if ($infoDocumento) {
                        $documentosInfo[] = [
                            'id_documentos' => $documento->id_documentos,
                            'documentos_nombre' => $documento->documentos_nombre,
                            'documentos_ruta' => $documento->documentos_ruta,
                            'documentos_extension' => $documento->documentos_extension,
                            'documentos_fecha_subida' => $documento->documentos_fecha_subida,
                            'categorias_documentos_nombre' => $infoDocumento['categorias_documentos_nombre'] ?? '',
                            'ordenes_aplicaciones_codigo' => $infoDocumento['ordenes_aplicaciones_codigo'] ?? '',
                            'aplicacion_nombre' => $infoDocumento['aplicacion_nombre'] ?? '',
                            'id_aplicacion' => $infoDocumento['id_aplicacion'] ?? '',
                            'autor' => ($infoDocumento['persona_nombres'] ?? '') . ' ' . ($infoDocumento['persona_apellidos'] ?? '')
                        ];
                    }
                }
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $documentosInfo,
                'total_resultados' => count($documentosInfo)
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al realizar la búsqueda',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
