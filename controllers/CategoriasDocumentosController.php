<?php

namespace Controllers;

use Exception;
use Model\CategoriasDocumentos;
use MVC\Router;

class CategoriasDocumentosController
{
    public static function index(Router $router)
    {
        $router->render('api/categorias-documentos', [
            'titulo' => 'Gestión de Categorías de Documentos'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $categorias = CategoriasDocumentos::where('categorias_documentos_situacion', $situacion);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $categorias
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener categorías',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['categorias_documentos_nombre'];

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
            $id = $datos['id_categorias_documentos'] ?? null;

            if ($id) {
                $categoria = CategoriasDocumentos::find($id);
                if (!$categoria) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Categoría no encontrada'
                    ]);
                    return;
                }
            } else {
                $categoria = new CategoriasDocumentos();
            }

            $categoria->sincronizar($datos);
            $resultado = $categoria->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Categoría guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la categoría'
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
            $categoria = CategoriasDocumentos::find($datos['id']);

            if (!$categoria) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Categoría no encontrada'
                ]);
                return;
            }

            $categoria->categorias_documentos_situacion = 0;
            $resultado = $categoria->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Categoría eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la categoría'
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
            $categoria = CategoriasDocumentos::find($datos['id']);

            if (!$categoria) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Categoría no encontrada'
                ]);
                return;
            }

            $categoria->categorias_documentos_situacion = 1;
            $resultado = $categoria->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Categoría recuperada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la categoría'
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
