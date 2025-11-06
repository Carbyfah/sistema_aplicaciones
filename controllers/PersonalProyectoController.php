<?php

namespace Controllers;

use Exception;
use Model\PersonalProyecto;
use MVC\Router;

class PersonalProyectoController
{
    public static function index(Router $router)
    {
        $router->render('api/personal-proyecto', [
            'titulo' => 'Asignación de Personal a Proyectos'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $proyecto_id = $_GET['proyecto_id'] ?? null;
            $persona_id = $_GET['persona_id'] ?? null;

            $query = "SELECT pp.*, p.persona_nombres, p.persona_apellidos, 
                 oa.ordenes_aplicaciones_codigo, a.aplicacion_nombre 
                 FROM personal_proyecto pp
                 JOIN persona p ON pp.persona_id_persona = p.id_persona
                 JOIN ordenes_aplicaciones oa ON pp.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
                 JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
                 WHERE pp.personal_proyecto_situacion = " . intval($situacion);

            if ($proyecto_id) {
                $query .= " AND pp.ordenes_aplicaciones_id_ordenes_aplicaciones = " . intval($proyecto_id);
            }

            if ($persona_id) {
                $query .= " AND pp.persona_id_persona = " . intval($persona_id);
            }

            $asignaciones = PersonalProyecto::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $asignaciones
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener asignaciones',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['ordenes_aplicaciones_id_ordenes_aplicaciones', 'persona_id_persona'];

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
            $id = (!empty($datos['id_personal_proyecto'])) ? $datos['id_personal_proyecto'] : null;

            if (!$id) {
                $query = "SELECT * FROM personal_proyecto 
                    WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = " . intval($datos['ordenes_aplicaciones_id_ordenes_aplicaciones']) .
                    " AND persona_id_persona = " . intval($datos['persona_id_persona']) .
                    " AND personal_proyecto_situacion = 1";

                $existente = PersonalProyecto::fetchFirst($query);

                if ($existente) {
                    http_response_code(400);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Esta persona ya está asignada a este proyecto'
                    ]);
                    return;
                }
            }

            if ($id) {
                $asignacion = PersonalProyecto::find($id);
                if (!$asignacion) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Asignación no encontrada'
                    ]);
                    return;
                }
            } else {
                $asignacion = new PersonalProyecto();
            }

            $asignacion->sincronizar($datos);
            $resultado = $asignacion->guardar();

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Asignación guardada correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar la asignación'
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
            $asignacion = PersonalProyecto::find($datos['id']);

            if (!$asignacion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Asignación no encontrada'
                ]);
                return;
            }

            $asignacion->personal_proyecto_situacion = 0;
            $resultado = $asignacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Asignación eliminada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar la asignación'
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
            $asignacion = PersonalProyecto::find($datos['id']);

            if (!$asignacion) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Asignación no encontrada'
                ]);
                return;
            }

            $asignacion->personal_proyecto_situacion = 1;
            $resultado = $asignacion->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Asignación recuperada correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar la asignación'
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
