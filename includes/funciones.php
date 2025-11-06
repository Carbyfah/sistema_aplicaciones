<?php

function debuguear($variable)
{
    echo "<pre>";
    var_dump($variable);
    echo "</pre>";
    exit;
}

function s($html)
{
    $s = htmlspecialchars($html);
    return $s;
}

function isAuth()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (!isset($_SESSION['login']) || $_SESSION['login'] !== true) {
        header('Location: /' . $_ENV['APP_NAME'] . '/');
        exit;
    }
}
function isAuthApi()
{
    getHeadersApi();
    session_start();
    if (!isset($_SESSION['auth_user'])) {
        echo json_encode([
            "mensaje" => "No esta autenticado",
            "codigo" => 4,
        ]);
        exit;
    }
}

function isNotAuth()
{
    session_start();
    if (isset($_SESSION['login']) && $_SESSION['login'] === true) {
        header('Location: /' . $_ENV['APP_NAME'] . '/dashboard');
        exit;
    }
}

function hasPermission(array $permisos)
{
    $comprobaciones = [];
    foreach ($permisos as $permiso) {
        $comprobaciones[] = !isset($_SESSION[$permiso]) ? false : true;
    }
    if (array_search(true, $comprobaciones) !== false) {
    } else {
        header('Location: /');
    }
}

function hasPermissionApi(array $permisos)
{
    getHeadersApi();
    $comprobaciones = [];
    foreach ($permisos as $permiso) {
        $comprobaciones[] = !isset($_SESSION[$permiso]) ? false : true;
    }
    if (array_search(true, $comprobaciones) !== false) {
    } else {
        echo json_encode([
            "mensaje" => "No tiene permisos",
            "codigo" => 4,
        ]);
        exit;
    }
}

function getHeadersApi()
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type, Authorization');
    header('Content-Type: application/json; charset=utf-8');
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(200);
        exit();
    }
    $method = $_SERVER['REQUEST_METHOD'];
    if ($method === 'POST' || $method === 'PUT' || $method === 'DELETE') {
        $json = file_get_contents('php://input');
        $data = json_decode($json, true);
        return $data ?? [];
    }
    return [];
}

function jsonResponse($data, $httpCode = 200)
{
    header('Content-Type: application/json; charset=utf-8');
    http_response_code($httpCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function asset($ruta)
{
    return "/" . $_ENV['APP_NAME'] . "/public/" . $ruta;
}

// ===============================================
// SISTEMA DE PERMISOS GRANULARES
// ===============================================

/**
 * Verifica si el usuario tiene un permiso específico
 * @param string $permiso Código del permiso (ej: "usuarios.ver")
 * @return bool
 */
function tienePermiso($permiso)
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // Si no hay sesión activa, no tiene permisos
    if (!isset($_SESSION['login']) || !$_SESSION['login']) {
        return false;
    }

    // Si no hay permisos cargados en sesión, no tiene permisos
    if (!isset($_SESSION['permisos'])) {
        return false;
    }

    // Administrador (rol_id = 1) tiene TODOS los permisos
    if (isset($_SESSION['rol_id']) && $_SESSION['rol_id'] == 1) {
        return true;
    }

    // Verificar si tiene el permiso específico
    if (in_array($permiso, $_SESSION['permisos'])) {
        return true;
    }

    // Verificar si tiene el permiso "gestionar" del módulo (permiso maestro)
    $partes = explode('.', $permiso);
    if (count($partes) == 2) {
        $moduloGestionar = $partes[0] . '.gestionar';
        if (in_array($moduloGestionar, $_SESSION['permisos'])) {
            return true;
        }
    }

    return false;
}

/**
 * Verifica si el usuario tiene al menos uno de los permisos de la lista
 * @param array $permisos Array de códigos de permisos
 * @return bool
 */
function tieneAlgunPermiso($permisos)
{
    foreach ($permisos as $permiso) {
        if (tienePermiso($permiso)) {
            return true;
        }
    }
    return false;
}

/**
 * Verifica si el usuario tiene TODOS los permisos de la lista
 * @param array $permisos Array de códigos de permisos
 * @return bool
 */
function tieneTodosLosPermisos($permisos)
{
    foreach ($permisos as $permiso) {
        if (!tienePermiso($permiso)) {
            return false;
        }
    }
    return true;
}

/**
 * Verifica permisos y redirige si no tiene acceso
 * @param string|array $permisos Permiso o array de permisos
 */
function verificarPermiso($permisos)
{
    if (is_array($permisos)) {
        if (!tieneAlgunPermiso($permisos)) {
            header('Location: /' . $_ENV['APP_NAME'] . '/dashboard');
            exit;
        }
    } else {
        if (!tienePermiso($permisos)) {
            header('Location: /' . $_ENV['APP_NAME'] . '/dashboard');
            exit;
        }
    }
}

/**
 * Verifica permisos en API y responde con JSON si no tiene acceso
 * @param string|array $permisos Permiso o array de permisos
 */
function verificarPermisoAPI($permisos)
{
    getHeadersApi();

    $tieneAcceso = false;

    if (is_array($permisos)) {
        $tieneAcceso = tieneAlgunPermiso($permisos);
    } else {
        $tieneAcceso = tienePermiso($permisos);
    }

    if (!$tieneAcceso) {
        http_response_code(403);
        echo json_encode([
            'exito' => false,
            'mensaje' => 'No tienes permisos para realizar esta acción',
            'codigo' => 403
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }
}


function tienePermisoModulo($modulo, $accion = 'ver')
{
    if (!isset($_SESSION['login']) || !$_SESSION['login']) {
        return false;
    }

    if (!isset($_SESSION['permisos_modulos']) || !is_array($_SESSION['permisos_modulos'])) {
        return false;
    }

    if (!isset($_SESSION['permisos_modulos'][$modulo])) {
        return false;
    }

    $permisos = $_SESSION['permisos_modulos'][$modulo];

    switch ($accion) {
        case 'ver':
            return $permisos['ver'] == 1;
        case 'crear':
            return $permisos['crear'] == 1;
        case 'editar':
            return $permisos['editar'] == 1;
        case 'eliminar':
            return $permisos['eliminar'] == 1;
        case 'exportar_excel':
            return $permisos['exportar_excel'] == 1;
        case 'exportar_pdf':
            return $permisos['exportar_pdf'] == 1;
        default:
            return false;
    }
}

function tieneAlgunPermisoModulo($modulos)
{
    foreach ($modulos as $modulo) {
        if (tienePermisoModulo($modulo, 'ver')) {
            return true;
        }
    }
    return false;
}
