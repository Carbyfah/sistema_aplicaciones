<?php

namespace Controllers;

use Exception;
use Model\Persona;
use MVC\Router;

class PersonaController
{
    public static function index(Router $router)
    {
        $router->render('api/persona', [
            'titulo' => 'Gestión de Personal'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;

            $query = "SELECT 
                        p.*,
                        r.roles_persona_nombre as rol_nombre
                      FROM persona p
                      LEFT JOIN roles_persona r ON p.roles_persona_id_roles_persona = r.id_roles_persona
                      WHERE p.persona_situacion = {$situacion}";

            $personas = Persona::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $personas
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener personal',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['persona_nombres', 'persona_apellidos', 'persona_identidad'];

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
            $id = $datos['id_persona'] ?? null;

            if ($id) {
                $persona = Persona::find($id);
                if (!$persona) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Persona no encontrada'
                    ]);
                    return;
                }
            } else {
                $persona = new Persona();
            }

            $persona->sincronizar($datos);
            $resultado = $persona->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Persona guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la persona'
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
            $persona = Persona::find($datos['id']);

            if (!$persona) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Persona no encontrada'
                ]);
                return;
            }

            $persona->persona_situacion = 0;
            $resultado = $persona->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Persona eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la persona'
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
            $persona = Persona::find($datos['id']);

            if (!$persona) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Persona no encontrada'
                ]);
                return;
            }

            $persona->persona_situacion = 1;
            $resultado = $persona->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Persona recuperada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la persona'
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
