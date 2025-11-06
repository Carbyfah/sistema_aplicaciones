<?php

namespace Controllers;

use Exception;
use Model\Aplicacion;
use Model\Documentos;
use Model\OrdenesAplicaciones;
use Model\Persona;
use Model\TareasAplicaciones;
use Model\Usuarios;
use MVC\Router;

class EstadisticasController
{
    public static function index(Router $router)
    {
        $router->render('api/estadisticas', [
            'titulo' => 'Estadísticas'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $tipo = $_GET['tipo'] ?? 'global';
            $ambito = $_GET['ambito'] ?? 'global';
            $referencia_id = $_GET['referencia_id'] ?? null;

            // Generar estadísticas en tiempo real
            $datos = self::generarEstadisticas($tipo, $ambito, $referencia_id);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $datos,
                'desde_cache' => false,
                'fecha_generacion' => date('Y-m-d H:i:s')
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener estadísticas',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    private static function generarEstadisticas($tipo, $ambito, $referencia_id = null)
    {
        switch ($ambito) {
            case 'global':
                return self::generarEstadisticasGlobales($tipo);
            case 'proyecto':
                return self::generarEstadisticasProyecto($tipo, $referencia_id);
            case 'usuario':
                return self::generarEstadisticasUsuario($tipo, $referencia_id);
            default:
                return [];
        }
    }

    private static function generarEstadisticasGlobales($tipo)
    {
        $datos = [];

        // Total de proyectos
        $query = "SELECT COUNT(*) as total FROM aplicacion WHERE aplicacion_situacion = 1";
        $resultado = Aplicacion::fetchFirst($query);
        $datos['total_proyectos'] = $resultado['total'] ?? 0;

        // Proyectos por estado
        $query = "SELECT e.estados_nombre, e.estados_color, COUNT(*) as cantidad 
                 FROM ordenes_aplicaciones oa
                 JOIN estados e ON oa.estados_id_estados = e.id_estados
                 WHERE oa.ordenes_aplicaciones_situacion = 1
                 GROUP BY e.id_estados
                 ORDER BY cantidad DESC";
        $datos['proyectos_por_estado'] = OrdenesAplicaciones::fetchArray($query);

        // Total de documentos
        $query = "SELECT COUNT(*) as total FROM documentos 
                 WHERE documentos_situacion = 1";
        $resultado = Documentos::fetchFirst($query);
        $datos['total_documentos'] = $resultado['total'] ?? 0;

        // Documentos por categoría
        $query = "SELECT c.categorias_documentos_nombre, COUNT(*) as cantidad 
                 FROM documentos d
                 JOIN categorias_documentos c ON d.categorias_documentos_id_categorias_documentos = c.id_categorias_documentos
                 WHERE d.documentos_situacion = 1
                 GROUP BY c.id_categorias_documentos
                 ORDER BY cantidad DESC";
        $datos['documentos_por_categoria'] = Documentos::fetchArray($query);

        // Total de personal
        $query = "SELECT COUNT(*) as total FROM persona 
                 WHERE persona_situacion = 1";
        $resultado = Persona::fetchFirst($query);
        $datos['total_personal'] = $resultado['total'] ?? 0;

        // Proyectos por programador
        $query = "SELECT p.persona_nombres, p.persona_apellidos, COUNT(*) as cantidad 
                 FROM ordenes_aplicaciones oa
                 JOIN usuarios u ON oa.usuarios_id_usuarios = u.id_usuarios
                 JOIN persona p ON u.persona_id_persona = p.id_persona
                 WHERE oa.ordenes_aplicaciones_situacion = 1
                 GROUP BY u.id_usuarios
                 ORDER BY cantidad DESC
                 LIMIT 10";
        $datos['proyectos_por_programador'] = OrdenesAplicaciones::fetchArray($query);

        // Tareas pendientes y completadas
        $query = "SELECT 
                 SUM(CASE WHEN tareas_aplicaciones_completada = 0 THEN 1 ELSE 0 END) as pendientes,
                 SUM(CASE WHEN tareas_aplicaciones_completada = 1 THEN 1 ELSE 0 END) as completadas
                 FROM tareas_aplicaciones
                 WHERE tareas_aplicaciones_situacion = 1";
        $resultado = TareasAplicaciones::fetchFirst($query);
        $datos['tareas'] = [
            'pendientes' => $resultado['pendientes'] ?? 0,
            'completadas' => $resultado['completadas'] ?? 0
        ];

        return $datos;
    }

    private static function generarEstadisticasProyecto($tipo, $proyecto_id)
    {
        if (!$proyecto_id) {
            return [];
        }

        $datos = [];

        // Total de proyectos (1 en este caso)
        $datos['total_proyectos'] = 1;

        // Obtener la orden de aplicación para este proyecto
        $queryOrden = "SELECT id_ordenes_aplicaciones FROM ordenes_aplicaciones 
                   WHERE aplicacion_id_aplicacion = " . intval($proyecto_id) . " 
                   AND ordenes_aplicaciones_situacion = 1 LIMIT 1";
        $orden = OrdenesAplicaciones::fetchFirst($queryOrden);

        if (!$orden) {
            return self::datosVacios();
        }

        $ordenId = $orden['id_ordenes_aplicaciones'];

        // Proyectos por estado (solo este proyecto)
        $query = "SELECT e.estados_nombre, e.estados_color, 1 as cantidad 
             FROM ordenes_aplicaciones oa
             JOIN estados e ON oa.estados_id_estados = e.id_estados
             WHERE oa.id_ordenes_aplicaciones = {$ordenId}";
        $datos['proyectos_por_estado'] = OrdenesAplicaciones::fetchArray($query);

        // Total de documentos del proyecto
        $query = "SELECT COUNT(*) as total FROM documentos 
             WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$ordenId}
             AND documentos_situacion = 1";
        $resultado = Documentos::fetchFirst($query);
        $datos['total_documentos'] = $resultado['total'] ?? 0;

        // Documentos por categoría del proyecto
        $query = "SELECT c.categorias_documentos_nombre, COUNT(*) as cantidad 
             FROM documentos d
             JOIN categorias_documentos c ON d.categorias_documentos_id_categorias_documentos = c.id_categorias_documentos
             WHERE d.ordenes_aplicaciones_id_ordenes_aplicaciones = {$ordenId}
             AND d.documentos_situacion = 1
             GROUP BY c.id_categorias_documentos
             ORDER BY cantidad DESC";
        $datos['documentos_por_categoria'] = Documentos::fetchArray($query);

        // Total de personal del proyecto
        $query = "SELECT COUNT(*) as total FROM personal_proyecto 
             WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$ordenId}
             AND personal_proyecto_situacion = 1";
        $resultado = OrdenesAplicaciones::fetchFirst($query);
        $datos['total_personal'] = $resultado['total'] ?? 0;

        // Personal del proyecto (para la gráfica de programadores)
        $query = "SELECT p.persona_nombres, p.persona_apellidos, 1 as cantidad 
             FROM personal_proyecto pp
             JOIN persona p ON pp.persona_id_persona = p.id_persona
             WHERE pp.ordenes_aplicaciones_id_ordenes_aplicaciones = {$ordenId}
             AND pp.personal_proyecto_situacion = 1";
        $datos['proyectos_por_programador'] = OrdenesAplicaciones::fetchArray($query);

        // Tareas del proyecto
        $query = "SELECT 
             SUM(CASE WHEN tareas_aplicaciones_completada = 0 THEN 1 ELSE 0 END) as pendientes,
             SUM(CASE WHEN tareas_aplicaciones_completada = 1 THEN 1 ELSE 0 END) as completadas
             FROM tareas_aplicaciones
             WHERE ordenes_aplicaciones_id_ordenes_aplicaciones = {$ordenId}
             AND tareas_aplicaciones_situacion = 1";
        $resultado = TareasAplicaciones::fetchFirst($query);
        $datos['tareas'] = [
            'pendientes' => $resultado['pendientes'] ?? 0,
            'completadas' => $resultado['completadas'] ?? 0
        ];

        return $datos;
    }

    private static function generarEstadisticasUsuario($tipo, $usuario_id)
    {
        if (!$usuario_id) {
            return [];
        }

        $datos = [];

        // Total de proyectos del usuario
        $query = "SELECT COUNT(*) as total FROM ordenes_aplicaciones 
             WHERE usuarios_id_usuarios = " . intval($usuario_id) . " 
             AND ordenes_aplicaciones_situacion = 1";
        $resultado = OrdenesAplicaciones::fetchFirst($query);
        $datos['total_proyectos'] = $resultado['total'] ?? 0;

        // Proyectos por estado del usuario
        $query = "SELECT e.estados_nombre, e.estados_color, COUNT(*) as cantidad 
             FROM ordenes_aplicaciones oa
             JOIN estados e ON oa.estados_id_estados = e.id_estados
             WHERE oa.usuarios_id_usuarios = " . intval($usuario_id) . "
             AND oa.ordenes_aplicaciones_situacion = 1
             GROUP BY e.id_estados
             ORDER BY cantidad DESC";
        $datos['proyectos_por_estado'] = OrdenesAplicaciones::fetchArray($query);

        // Total de documentos del usuario
        $query = "SELECT COUNT(*) as total FROM documentos 
             WHERE usuarios_id_usuarios = " . intval($usuario_id) . "
             AND documentos_situacion = 1";
        $resultado = Documentos::fetchFirst($query);
        $datos['total_documentos'] = $resultado['total'] ?? 0;

        // Documentos por categoría del usuario
        $query = "SELECT c.categorias_documentos_nombre, COUNT(*) as cantidad 
             FROM documentos d
             JOIN categorias_documentos c ON d.categorias_documentos_id_categorias_documentos = c.id_categorias_documentos
             WHERE d.usuarios_id_usuarios = " . intval($usuario_id) . "
             AND d.documentos_situacion = 1
             GROUP BY c.id_categorias_documentos
             ORDER BY cantidad DESC";
        $datos['documentos_por_categoria'] = Documentos::fetchArray($query);

        // Total de personal (no aplica por usuario, ponemos 1)
        $datos['total_personal'] = 1;

        // "Programador" actual (el usuario seleccionado)
        $query = "SELECT p.persona_nombres, p.persona_apellidos, COUNT(*) as cantidad 
             FROM ordenes_aplicaciones oa
             JOIN usuarios u ON oa.usuarios_id_usuarios = u.id_usuarios
             JOIN persona p ON u.persona_id_persona = p.id_persona
             WHERE oa.usuarios_id_usuarios = " . intval($usuario_id) . "
             AND oa.ordenes_aplicaciones_situacion = 1
             GROUP BY u.id_usuarios";
        $datos['proyectos_por_programador'] = OrdenesAplicaciones::fetchArray($query);

        // Tareas del usuario
        $query = "SELECT 
             SUM(CASE WHEN ta.tareas_aplicaciones_completada = 0 THEN 1 ELSE 0 END) as pendientes,
             SUM(CASE WHEN ta.tareas_aplicaciones_completada = 1 THEN 1 ELSE 0 END) as completadas
             FROM tareas_aplicaciones ta
             JOIN ordenes_aplicaciones oa ON ta.ordenes_aplicaciones_id_ordenes_aplicaciones = oa.id_ordenes_aplicaciones
             WHERE oa.usuarios_id_usuarios = " . intval($usuario_id) . "
             AND ta.tareas_aplicaciones_situacion = 1";
        $resultado = TareasAplicaciones::fetchFirst($query);
        $datos['tareas'] = [
            'pendientes' => $resultado['pendientes'] ?? 0,
            'completadas' => $resultado['completadas'] ?? 0
        ];

        return $datos;
    }

    private static function datosVacios()
    {
        return [
            'total_proyectos' => 0,
            'proyectos_por_estado' => [],
            'total_documentos' => 0,
            'documentos_por_categoria' => [],
            'total_personal' => 0,
            'proyectos_por_programador' => [],
            'tareas' => [
                'pendientes' => 0,
                'completadas' => 0
            ]
        ];
    }
}
