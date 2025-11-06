<?php

namespace Controllers;

use Exception;
use Model\OrdenesAplicaciones;
use MVC\Router;

class OrdenesAplicacionesController
{
    public static function index(Router $router)
    {
        $router->render('api/ordenes-aplicaciones', [
            'titulo' => 'Gestión de Proyectos Asignados'
        ]);
    }

    public static function misProyectos(Router $router)
    {
        // Verificar autenticación
        isAuth();

        $router->render('pages/mis-proyectos', [
            'titulo' => 'Mis Proyectos'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;
            $limite = $_GET['limit'] ?? null;
            $usuario_actual = $_GET['usuario_actual'] ?? null;

            $query = "SELECT oa.*, a.aplicacion_nombre, a.aplicacion_desc_corta, 
                 e.estados_nombre, e.estados_color, u.usuarios_nombre,
                 p.persona_nombres, p.persona_apellidos 
                 FROM ordenes_aplicaciones oa
                 JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
                 JOIN estados e ON oa.estados_id_estados = e.id_estados
                 JOIN usuarios u ON oa.usuarios_id_usuarios = u.id_usuarios
                 JOIN persona p ON u.persona_id_persona = p.id_persona
                 WHERE oa.ordenes_aplicaciones_situacion = " . intval($situacion);

            if ($usuario_actual && isset($_SESSION['usuario_id'])) {
                $query .= " AND oa.usuarios_id_usuarios = " . intval($_SESSION['usuario_id']);
            }

            if ($limite) {
                $query .= " LIMIT " . intval($limite);
            }

            $ordenes = OrdenesAplicaciones::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $ordenes
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener proyectos asignados',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['aplicacion_id_aplicacion', 'usuarios_id_usuarios', 'estados_id_estados', 'ordenes_aplicaciones_fecha_entrega'];

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
            $id = $datos['id_ordenes_aplicaciones'] ?? null;

            if ($id) {
                $orden = OrdenesAplicaciones::find($id);
                if (!$orden) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Proyecto asignado no encontrado'
                    ]);
                    return;
                }
            } else {
                $orden = new OrdenesAplicaciones();
                $orden->ordenes_aplicaciones_codigo = 'PRY-' . date('Ymd') . '-' . rand(1000, 9999);
                $orden->ordenes_aplicaciones_fecha_asignacion = date('Y-m-d H:i:s');
            }

            $orden->sincronizar($datos);
            $resultado = $orden->guardar();

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto asignado guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el proyecto asignado'
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
            $orden = OrdenesAplicaciones::find($datos['id']);

            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto asignado no encontrado'
                ]);
                return;
            }

            $orden->ordenes_aplicaciones_situacion = 0;
            $resultado = $orden->guardar();

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto asignado eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el proyecto asignado'
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
            $orden = OrdenesAplicaciones::find($datos['id']);

            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto asignado no encontrado'
                ]);
                return;
            }

            $orden->ordenes_aplicaciones_situacion = 1;
            $resultado = $orden->guardar();

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto asignado recuperado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el proyecto asignado'
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

    public static function obtenerDetallesAPI()
    {
        getHeadersApi();

        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID no proporcionado'
            ]);
            return;
        }

        try {
            $id = intval($_GET['id']);

            $query = "SELECT oa.*, a.aplicacion_nombre, a.aplicacion_desc_corta, 
                     e.estados_nombre, e.estados_color, u.usuarios_nombre,
                     p.persona_nombres, p.persona_apellidos 
                     FROM ordenes_aplicaciones oa
                     JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
                     JOIN estados e ON oa.estados_id_estados = e.id_estados
                     JOIN usuarios u ON oa.usuarios_id_usuarios = u.id_usuarios
                     JOIN persona p ON u.persona_id_persona = p.id_persona
                     WHERE oa.id_ordenes_aplicaciones = " . $id;

            $resultado = OrdenesAplicaciones::fetchFirst($query);

            if (!$resultado) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto asignado no encontrado'
                ]);
                return;
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $resultado
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener detalles del proyecto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function marcarRecibidaAPI()
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
            $orden = OrdenesAplicaciones::find($datos['id']);

            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado'
                ]);
                return;
            }

            $estadoPendiente = self::obtenerEstadoId('Pendiente');
            $estadoEnProceso = self::obtenerEstadoId('En Proceso');

            if ($orden->estados_id_estados == $estadoEnProceso) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'El proyecto ya está en proceso'
                ]);
                return;
            }

            if ($orden->estados_id_estados != $estadoPendiente) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Solo se pueden marcar como recibidos los proyectos pendientes'
                ]);
                return;
            }

            $orden->estados_id_estados = $estadoEnProceso;
            $resultado = $orden->guardar();

            if ($resultado['resultado'] > 0) {
                http_response_code(200);
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto marcado como recibido'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al marcar el proyecto'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al marcar el proyecto',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    private static function obtenerEstadoId($nombreEstado)
    {
        $query = "SELECT id_estados FROM estados WHERE estados_nombre = '{$nombreEstado}' LIMIT 1";
        $resultado = OrdenesAplicaciones::fetchFirst($query);
        return $resultado['id_estados'] ?? null;
    }

    public static function marcarCompletadoAPI()
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
            $orden = OrdenesAplicaciones::find($datos['id']);

            if (!$orden) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado'
                ]);
                return;
            }

            // Verificar si todas las tareas están completadas
            $query = "SELECT COUNT(*) as total FROM tareas_aplicaciones 
                 WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$datos['id']} 
                 AND tareas_aplicaciones_situacion = 1 
                 AND tareas_aplicaciones_completada = 0";

            $tareasIncompletas = OrdenesAplicaciones::fetchFirst($query);

            if ($tareasIncompletas && $tareasIncompletas['total'] > 0) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'No se puede marcar como completado. Hay tareas pendientes.'
                ]);
                return;
            }

            // Obtener ID del estado "Completado"
            $estadoCompletado = self::obtenerEstadoId('Completado');

            if (!$estadoCompletado) {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'No se encontró el estado Completado en el sistema'
                ]);
                return;
            }

            // Actualizar el estado del proyecto
            $orden->estados_id_estados = $estadoCompletado;
            $resultado = $orden->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Proyecto marcado como Completado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al marcar el proyecto como completado'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al procesar la solicitud',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function obtenerDetallesCompletosAPI()
    {
        getHeadersApi();

        if (!isset($_GET['id'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'ID no proporcionado'
            ]);
            return;
        }

        try {
            $id = intval($_GET['id']);

            // INFORMACIÓN GENERAL DEL PROYECTO
            $queryGeneral = "SELECT oa.*, 
                        a.aplicacion_nombre, a.aplicacion_desc_corta, a.aplicacion_larga,
                        e.estados_nombre, e.estados_color, 
                        u.usuarios_nombre,
                        p.persona_nombres, p.persona_apellidos, p.persona_correo, p.persona_telefono,
                        creador.persona_nombres as creado_por_nombre, 
                        creador.persona_apellidos as creado_por_apellido
                        FROM ordenes_aplicaciones oa
                        JOIN aplicacion a ON oa.aplicacion_id_aplicacion = a.id_aplicacion
                        JOIN estados e ON oa.estados_id_estados = e.id_estados
                        JOIN usuarios u ON oa.usuarios_id_usuarios = u.id_usuarios
                        JOIN persona p ON u.persona_id_persona = p.id_persona
                        LEFT JOIN usuarios creador_user ON a.creado_por = creador_user.id_usuarios
                        LEFT JOIN persona creador ON creador_user.persona_id_persona = creador.id_persona
                        WHERE oa.id_ordenes_aplicaciones = {$id}";

            $general = OrdenesAplicaciones::fetchFirst($queryGeneral);

            if (!$general) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Proyecto no encontrado'
                ]);
                return;
            }

            // EQUIPO DE TRABAJO
            $queryEquipo = "SELECT pp.*, 
                        p.persona_nombres, p.persona_apellidos, 
                        p.persona_correo, p.persona_telefono,
                        r.roles_persona_nombre
                        FROM personal_proyecto pp
                        JOIN persona p ON pp.persona_id_persona = p.id_persona
                        LEFT JOIN roles_persona r ON p.roles_persona_id_roles_persona = r.id_roles_persona
                        WHERE pp.ordenes_aplicaciones_id_ordenes_aplicaciones = {$id}
                        AND pp.personal_proyecto_situacion = 1
                        ORDER BY pp.personal_proyecto_fecha_asignacion DESC";

            $equipo = OrdenesAplicaciones::fetchArray($queryEquipo);

            // TAREAS
            $queryTareas = "SELECT ta.*, 
                        u.usuarios_nombre,
                        p.persona_nombres, p.persona_apellidos
                        FROM tareas_aplicaciones ta
                        JOIN usuarios u ON ta.usuarios_id_usuarios = u.id_usuarios
                        JOIN persona p ON u.persona_id_persona = p.id_persona
                        WHERE ta.ordenes_aplicaciones_id_ordenes_aplicaciones = {$id}
                        AND ta.tareas_aplicaciones_situacion = 1
                        ORDER BY ta.tareas_aplicaciones_completada ASC, 
                                 FIELD(ta.tareas_aplicaciones_prioridad, 'Alta', 'Media', 'Baja'),
                                 ta.tareas_aplicaciones_fecha_limite ASC";

            $tareas = OrdenesAplicaciones::fetchArray($queryTareas);

            // PROGRESO DE TAREAS
            $queryProgreso = "SELECT 
                         COUNT(*) as total,
                         SUM(CASE WHEN tareas_aplicaciones_completada = 1 THEN 1 ELSE 0 END) as completadas
                         FROM tareas_aplicaciones
                         WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$id}
                         AND tareas_aplicaciones_situacion = 1";

            $progreso = OrdenesAplicaciones::fetchFirst($queryProgreso);

            // DOCUMENTOS
            $queryDocumentos = "SELECT d.*, 
                           cd.categorias_documentos_nombre,
                           u.usuarios_nombre,
                           p.persona_nombres, p.persona_apellidos
                           FROM documentos d
                           JOIN categorias_documentos cd ON d.categorias_documentos_id_categorias_documentos = cd.id_categorias_documentos
                           JOIN usuarios u ON d.usuarios_id_usuarios = u.id_usuarios
                           JOIN persona p ON u.persona_id_persona = p.id_persona
                           WHERE d.ordenes_aplicaciones_id_ordenes_aplicaciones = {$id}
                           AND d.documentos_situacion = 1
                           ORDER BY d.documentos_fecha_subida DESC";

            $documentos = OrdenesAplicaciones::fetchArray($queryDocumentos);

            // COSTOS
            $queryCostos = "SELECT ac.*, 
                       co.complejidad_nombre, co.complejidad_factor,
                       so.seguridad_nombre, so.seguridad_factor
                       FROM aplicacion_costos ac
                       LEFT JOIN complejidad_opciones co ON ac.complejidad_id = co.id_complejidad
                       LEFT JOIN seguridad_opciones so ON ac.seguridad_id = so.id_seguridad
                       WHERE ac.aplicacion_id_aplicacion = {$general['aplicacion_id_aplicacion']}
                       AND ac.costos_situacion = 1
                       ORDER BY ac.fecha_creacion DESC
                       LIMIT 1";

            $costos = OrdenesAplicaciones::fetchFirst($queryCostos);

            // BASE DE DATOS - TABLAS
            $queryTablas = "SELECT at.*, 
                       tt.tipos_tabla_nombre,
                       COUNT(ac.id_aplicacion_campos) as total_campos
                       FROM aplicacion_tablas at
                       LEFT JOIN tipos_tabla tt ON at.tipo_tabla_id = tt.id_tipo_tabla
                       LEFT JOIN aplicacion_campos ac ON at.id_aplicacion_tablas = ac.aplicacion_tablas_id 
                                                      AND ac.campos_situacion = 1
                       WHERE at.aplicacion_id_aplicacion = {$general['aplicacion_id_aplicacion']}
                       AND at.tablas_situacion = 1
                       GROUP BY at.id_aplicacion_tablas
                       ORDER BY at.tablas_nombre";

            $tablas = OrdenesAplicaciones::fetchArray($queryTablas);

            // RESPUESTA COMPLETA
            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => [
                    'general' => $general,
                    'equipo' => $equipo,
                    'tareas' => [
                        'lista' => $tareas,
                        'progreso' => $progreso
                    ],
                    'documentos' => $documentos,
                    'costos' => $costos,
                    'base_datos' => $tablas
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener detalles completos',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
