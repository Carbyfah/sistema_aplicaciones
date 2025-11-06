<?php

namespace Controllers;

use Exception;
use Model\Aplicacion;
use Model\OrdenesAplicaciones;
use Model\Usuarios;
use Model\Documentos;
use Model\LogsActividad;
use Model\Notificaciones;
use MVC\Router;

class APIController
{
    public static function index(Router $router)
    {
        // Verificar autenticación
        isAuth();

        $router->render('pages/index', [
            'titulo' => 'Dashboard'
        ]);
    }

    public static function checkAuthAPI()
    {
        getHeadersApi();

        try {
            $autenticado = isset($_SESSION['login']) && $_SESSION['login'] === true;

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'autenticado' => $autenticado,
                'usuario' => $autenticado ? [
                    'id' => $_SESSION['usuario_id'] ?? null,
                    'nombre' => $_SESSION['usuario_nombre'] ?? null,
                    'persona_id' => $_SESSION['persona_id'] ?? null,
                    'rol' => $_SESSION['rol'] ?? null
                ] : null
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al verificar autenticación',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function menuAPI()
    {
        getHeadersApi();

        try {
            $menu = [
                [
                    'nombre' => 'Dashboard',
                    'icono' => 'fas fa-tachometer-alt',
                    'url' => '/dashboard',
                    'hijos' => []
                ],
                [
                    'nombre' => 'Mis Proyectos',
                    'icono' => 'fas fa-tasks',
                    'url' => '/mis-proyectos',
                    'hijos' => []
                ],
                [
                    'nombre' => 'Proyectos',
                    'icono' => 'fas fa-project-diagram',
                    'url' => '#',
                    'hijos' => [
                        ['nombre' => 'Catálogo de Proyectos', 'url' => '/proyectos'],
                        ['nombre' => 'Proyectos Asignados', 'url' => '/proyectos-asignados'],
                        ['nombre' => 'Estados', 'url' => '/estados']
                    ]
                ],
                [
                    'nombre' => 'Documentos',
                    'icono' => 'fas fa-folder-open',
                    'url' => '#',
                    'hijos' => [
                        ['nombre' => 'Todos los Documentos', 'url' => '/documentos'],
                        ['nombre' => 'Categorías', 'url' => '/categorias'],
                        ['nombre' => 'Editor de Contenido', 'url' => '/editor']
                    ]
                ],
                [
                    'nombre' => 'Personal',
                    'icono' => 'fas fa-users',
                    'url' => '#',
                    'hijos' => [
                        ['nombre' => 'Gestión de Personal', 'url' => '/personal'],
                        ['nombre' => 'Asignaciones', 'url' => '/personal-proyecto'],
                        ['nombre' => 'Tareas', 'url' => '/tareas']
                    ]
                ],
                [
                    'nombre' => 'Estadísticas',
                    'icono' => 'fas fa-chart-bar',
                    'url' => '/estadisticas',
                    'hijos' => []
                ],
                [
                    'nombre' => 'Configuración',
                    'icono' => 'fas fa-cog',
                    'url' => '#',
                    'hijos' => [
                        ['nombre' => 'Usuarios', 'url' => '/usuarios'],
                        ['nombre' => 'Roles', 'url' => '/roles'],
                        ['nombre' => 'Módulos', 'url' => '/modulos'],
                        ['nombre' => 'Permisos de Usuarios', 'url' => '/usuarios-permisos'],
                        ['nombre' => 'Sistema', 'url' => '/sistema']
                    ]
                ],
                [
                    'nombre' => 'Auditoría',
                    'icono' => 'fas fa-clipboard-list',
                    'url' => '#',
                    'hijos' => [
                        ['nombre' => 'Logs de Actividad', 'url' => '/logs'],
                        ['nombre' => 'Registro de Sesiones', 'url' => '/sesiones'],
                        ['nombre' => 'Intentos de Login', 'url' => '/intentos-login']
                    ]
                ]
            ];

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $menu
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener menú',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function tienePermisoAPI()
    {
        getHeadersApi();

        try {
            $permiso = $_GET['permiso'] ?? null;

            if (!$permiso) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Permiso no especificado'
                ]);
                return;
            }

            $tienePermiso = isset($_SESSION[$permiso]) && $_SESSION[$permiso] === true;

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'tiene_permiso' => $tienePermiso
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al verificar permiso',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function estadisticasRapidasAPI()
    {
        getHeadersApi();

        try {
            // Obtener datos directamente con una sola consulta para asegurar consistencia
            $query = "SELECT
            (SELECT COUNT(*) FROM aplicacion WHERE aplicacion_situacion = 1) as proyectos_activos,
            (SELECT COUNT(*) FROM ordenes_aplicaciones oa 
             JOIN estados e ON oa.estados_id_estados = e.id_estados
             WHERE oa.ordenes_aplicaciones_situacion = 1 
             AND e.estados_nombre = 'Completado') as proyectos_completados,
            (SELECT COUNT(*) FROM documentos WHERE documentos_situacion = 1) as total_documentos,
            (SELECT COUNT(*) FROM tareas_aplicaciones 
             WHERE tareas_aplicaciones_situacion = 1 
             AND tareas_aplicaciones_completada = 0) as tareas_pendientes";

            $resultado = OrdenesAplicaciones::fetchFirst($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => [
                    'total_proyectos' => $resultado['proyectos_activos'] ?? 0,
                    'total_asignados' => $resultado['proyectos_completados'] ?? 0,
                    'total_documentos' => $resultado['total_documentos'] ?? 0,
                    'total_usuarios' => $resultado['tareas_pendientes'] ?? 0
                ]
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

    public static function getConfiguracionAPI()
    {
        getHeadersApi();

        try {
            $configuracion = [
                'nombre_sistema' => $_ENV['APP_NAME'] ?? 'Sistema Aplicaciones',
                'version' => '1.0',
                'ambiente' => $_ENV['APP_ENV'] ?? 'production',
                'fecha_actual' => date('Y-m-d H:i:s')
            ];

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $configuracion
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener configuración',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
