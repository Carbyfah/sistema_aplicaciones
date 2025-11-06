<?php
// controllers/UsuariosController.php (MODIFICADO)

namespace Controllers;

use Exception;
use Model\Usuarios;
use Model\UsuariosPermisos;
use MVC\Router;

class UsuariosController
{
    public static function index(Router $router)
    {
        $router->render('api/usuarios', [
            'titulo' => 'Gestión de Usuarios'
        ]);
    }

    public static function permisosVista(Router $router)
    {
        isAuth();

        $router->render('api/usuarios-permisos', [
            'titulo' => 'Asignar Permisos a Usuario'
        ]);
    }

    public static function login(Router $router)
    {
        isNotAuth();

        $router->render('auth/login', [
            'titulo' => 'Iniciar Sesión'
        ], false);
    }

    public static function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        try {
            if (isset($_SESSION['sesion_token'])) {
                $token = $_SESSION['sesion_token'];
                $tokenEscapado = addslashes($token);

                $queryClose = "UPDATE sesiones_usuarios 
                          SET sesion_estado = 0, 
                              sesion_fecha_cierre = NOW() 
                          WHERE sesion_token = '{$tokenEscapado}'";

                Usuarios::SQL($queryClose);
            }
        } catch (Exception $e) {
            error_log("Error al cerrar sesión en BD: " . $e->getMessage());
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();

        header('Location: /' . $_ENV['APP_NAME'] . '/');
        exit;
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $situacion = $_GET['situacion'] ?? 1;

            $query = "SELECT 
                u.*,
                p.persona_nombres,
                p.persona_apellidos,
                p.persona_identidad,
                r.roles_persona_nombre as rol_nombre,
                CONCAT(p.persona_nombres, ' ', p.persona_apellidos, ' (', p.persona_identidad, ')') as nombre_completo_identidad
            FROM usuarios u
            LEFT JOIN persona p ON u.persona_id_persona = p.id_persona
            LEFT JOIN roles_persona r ON p.roles_persona_id_roles_persona = r.id_roles_persona
            WHERE u.usuarios_situacion = {$situacion}";

            $usuarios = Usuarios::fetchArray($query);

            foreach ($usuarios as &$usuario) {
                $usuario['usuarios_password'] = '';
            }

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $usuarios
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener usuarios',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function guardarAPI()
    {
        $datos = getHeadersApi();

        $campos = ['usuarios_nombre', 'persona_id_persona'];

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
            $id = $datos['id_usuarios'] ?? null;

            if ($id) {
                $usuario = Usuarios::find($id);
                if (!$usuario) {
                    http_response_code(404);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => 'Usuario no encontrado'
                    ]);
                    return;
                }
            } else {
                $usuario = new Usuarios();

                if (!isset($datos['usuarios_password']) || trim($datos['usuarios_password']) === '') {
                    http_response_code(400);
                    echo json_encode([
                        'exito' => false,
                        'mensaje' => "La contraseña es requerida para nuevos usuarios"
                    ]);
                    return;
                }

                $datos['usuarios_password'] = password_hash($datos['usuarios_password'], PASSWORD_BCRYPT);
            }

            if ($id && isset($datos['usuarios_password']) && !empty(trim($datos['usuarios_password']))) {
                $datos['usuarios_password'] = password_hash($datos['usuarios_password'], PASSWORD_BCRYPT);
            } elseif ($id) {
                unset($datos['usuarios_password']);
            }

            $usuario->sincronizar($datos);
            $resultado = $usuario->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Usuario guardado correctamente',
                    'id' => $id ?? $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al guardar el usuario'
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
            $usuario = Usuarios::find($datos['id']);

            if (!$usuario) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            $usuario->usuarios_situacion = 0;
            $resultado = $usuario->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Usuario eliminado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al eliminar el usuario'
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
            $usuario = Usuarios::find($datos['id']);

            if (!$usuario) {
                http_response_code(404);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario no encontrado'
                ]);
                return;
            }

            $usuario->usuarios_situacion = 1;
            $resultado = $usuario->guardar();

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Usuario recuperado correctamente'
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al recuperar el usuario'
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

    public static function loginAPI()
    {
        $datos = getHeadersApi();

        if (!isset($datos['usuarios_nombre']) || !isset($datos['usuarios_password'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Usuario y contraseña son requeridos'
            ]);
            return;
        }

        try {
            $usuarios = Usuarios::where('usuarios_nombre', $datos['usuarios_nombre']);

            if (empty($usuarios)) {
                http_response_code(401);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario o contraseña incorrectos'
                ]);
                return;
            }

            $usuario = $usuarios[0];

            if (!password_verify($datos['usuarios_password'], $usuario->usuarios_password)) {
                http_response_code(401);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario o contraseña incorrectos'
                ]);
                return;
            }

            if ($usuario->usuarios_situacion == 0) {
                http_response_code(401);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Usuario inactivo'
                ]);
                return;
            }

            $usuario->ultimo_acceso = date('Y-m-d H:i:s');
            $usuario->guardar();

            $_SESSION['login'] = true;
            $_SESSION['usuario_id'] = $usuario->id_usuarios;
            $_SESSION['usuario_nombre'] = $usuario->usuarios_nombre;
            $_SESSION['persona_id'] = $usuario->persona_id_persona;

            $query = "SELECT p.persona_nombres, p.persona_apellidos, p.roles_persona_id_roles_persona, r.roles_persona_nombre as rol 
          FROM persona p 
          JOIN roles_persona r ON p.roles_persona_id_roles_persona = r.id_roles_persona 
          WHERE p.id_persona = " . $_SESSION['persona_id'];

            $resultado = Usuarios::fetchFirst($query);

            if ($resultado) {
                $_SESSION['nombre'] = $resultado['persona_nombres'] . ' ' . $resultado['persona_apellidos'];
                $_SESSION['rol'] = $resultado['rol'];
                $_SESSION['rol_id'] = $resultado['roles_persona_id_roles_persona'];
            }

            $permisosModulos = [];

            $queryPermisosModulos = "SELECT 
                                        m.modulos_nombre,
                                        up.puede_ver,
                                        up.puede_crear,
                                        up.puede_editar,
                                        up.puede_eliminar,
                                        up.puede_exportar_excel,
                                        up.puede_exportar_pdf
                                     FROM usuarios_permisos up
                                     INNER JOIN modulos m ON up.modulos_id_modulos = m.id_modulos
                                     WHERE up.usuarios_id_usuarios = {$usuario->id_usuarios}
                                     AND m.modulos_situacion = 1";

            $permisosDB = UsuariosPermisos::fetchArray($queryPermisosModulos);

            foreach ($permisosDB as $permiso) {
                $permisosModulos[$permiso['modulos_nombre']] = [
                    'ver' => $permiso['puede_ver'],
                    'crear' => $permiso['puede_crear'],
                    'editar' => $permiso['puede_editar'],
                    'eliminar' => $permiso['puede_eliminar'],
                    'exportar_excel' => $permiso['puede_exportar_excel'],
                    'exportar_pdf' => $permiso['puede_exportar_pdf']
                ];
            }

            $_SESSION['permisos_modulos'] = $permisosModulos;

            $permisosSimples = [];
            foreach ($permisosModulos as $modulo => $permisos) {
                if ($permisos['ver'] == 1) {
                    $permisosSimples[] = $modulo . '.ver';
                }
                if ($permisos['crear'] == 1) {
                    $permisosSimples[] = $modulo . '.crear';
                }
                if ($permisos['editar'] == 1) {
                    $permisosSimples[] = $modulo . '.editar';
                }
                if ($permisos['eliminar'] == 1) {
                    $permisosSimples[] = $modulo . '.eliminar';
                }
            }
            $_SESSION['permisos'] = $permisosSimples;



            $sesionToken = bin2hex(random_bytes(32));
            $sesionIP = addslashes($_SERVER['REMOTE_ADDR'] ?? 'unknown');
            $sesionUserAgent = addslashes($_SERVER['HTTP_USER_AGENT'] ?? 'unknown');

            $querySesion = "INSERT INTO sesiones_usuarios (
            sesion_token,
            sesion_fecha_inicio,
            sesion_ip,
            sesion_user_agent,
            sesion_estado,
            usuarios_id_usuarios
        ) VALUES (
            '{$sesionToken}',
            NOW(),
            '{$sesionIP}',
            '{$sesionUserAgent}',
            1,
            {$usuario->id_usuarios}
        )";

            Usuarios::SQL($querySesion);

            $_SESSION['sesion_token'] = $sesionToken;

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'mensaje' => 'Login exitoso',
                'usuario' => [
                    'id' => $usuario->id_usuarios,
                    'nombre' => $usuario->usuarios_nombre,
                    'persona_id' => $usuario->persona_id_persona,
                    'nombre_completo' => $_SESSION['nombre'] ?? '',
                    'rol' => $_SESSION['rol'] ?? '',
                    'permisos_count' => count($permisosModulos)
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al iniciar sesión',
                'detalle' => $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    public static function verificarAdminAPI()
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            $query = "SELECT COUNT(*) as total 
                  FROM usuarios u 
                  INNER JOIN persona p ON u.persona_id_persona = p.id_persona 
                  WHERE p.roles_persona_id_roles_persona = 1 
                  AND u.usuarios_situacion = 1";

            $resultado = Usuarios::fetchFirst($query);

            $existeAdmin = $resultado['total'] > 0;

            echo json_encode([
                'exito' => true,
                'existe_admin' => $existeAdmin
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al verificar administrador',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function registrarPrimerAdminAPI()
    {
        header('Content-Type: application/json; charset=utf-8');

        $json = file_get_contents('php://input');
        $datos = json_decode($json, true);

        error_log("Datos recibidos: " . print_r($datos, true));

        $camposRequeridos = [
            'persona_nombres',
            'persona_apellidos',
            'persona_identidad',
            'usuarios_nombre',
            'usuarios_password'
        ];

        foreach ($camposRequeridos as $campo) {
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
            $query = "SELECT COUNT(*) as total 
                  FROM usuarios u 
                  INNER JOIN persona p ON u.persona_id_persona = p.id_persona 
                  WHERE p.roles_persona_id_roles_persona = 1 
                  AND u.usuarios_situacion = 1";

            $resultado = Usuarios::fetchFirst($query);

            if ($resultado && $resultado['total'] > 0) {
                http_response_code(403);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Ya existe un administrador en el sistema'
                ]);
                return;
            }

            $nombres = addslashes($datos['persona_nombres']);
            $apellidos = addslashes($datos['persona_apellidos']);
            $identidad = addslashes($datos['persona_identidad']);
            $nombreUsuario = addslashes($datos['usuarios_nombre']);

            $queryPersona = "INSERT INTO persona (
            persona_nombres, 
            persona_apellidos, 
            persona_identidad,
            roles_persona_id_roles_persona,
            persona_situacion
        ) VALUES (
            '$nombres',
            '$apellidos',
            '$identidad',
            1,
            1
        )";

            error_log("Query persona: " . $queryPersona);

            $resultadoPersona = Usuarios::SQL($queryPersona);

            if (!$resultadoPersona) {
                throw new Exception('Error al crear la persona en la base de datos');
            }

            $queryId = "SELECT LAST_INSERT_ID() as id";
            $resultadoId = Usuarios::fetchFirst($queryId);

            if (!$resultadoId || !isset($resultadoId['id'])) {
                throw new Exception('Error al obtener el ID de la persona creada');
            }

            $personaId = $resultadoId['id'];
            error_log("Persona creada con ID: " . $personaId);

            $passwordHash = password_hash($datos['usuarios_password'], PASSWORD_BCRYPT);

            $queryUsuario = "INSERT INTO usuarios (
            usuarios_nombre,
            usuarios_password,
            persona_id_persona,
            usuarios_situacion
        ) VALUES (
            '$nombreUsuario',
            '$passwordHash',
            $personaId,
            1
        )";

            error_log("Query usuario: " . $queryUsuario);

            $resultadoUsuario = Usuarios::SQL($queryUsuario);

            if (!$resultadoUsuario) {
                throw new Exception('Error al crear el usuario en la base de datos');
            }

            echo json_encode([
                'exito' => true,
                'mensaje' => 'Administrador creado correctamente. Ya puedes iniciar sesión.'
            ]);
        } catch (Exception $e) {
            error_log("Error en registrarPrimerAdminAPI: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());

            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al registrar administrador',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
