<?php

namespace Controllers;

use Exception;
use Model\IntentosLogin;
use MVC\Router;

class IntentosLoginController
{
    public static function index(Router $router)
    {
        $router->render('api/intentos-login', [
            'titulo' => 'Intentos de Login'
        ]);
    }

    public static function obtenerAPI()
    {
        getHeadersApi();

        try {
            $usuario = $_GET['usuario'] ?? null;
            $limite = $_GET['limite'] ?? 100;

            $query = "SELECT * FROM intentos_login";

            if ($usuario) {
                $query .= " WHERE usuario_nombre = " . IntentosLogin::$db->quote($usuario);
            }

            $query .= " ORDER BY intento_fecha DESC LIMIT " . intval($limite);

            $intentos = IntentosLogin::fetchArray($query);

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'data' => $intentos
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al obtener intentos de login',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function registrarIntentoAPI()
    {
        $datos = getHeadersApi();

        $campos = ['usuario_nombre', 'intento_exitoso'];

        foreach ($campos as $campo) {
            if (!isset($datos[$campo])) {
                http_response_code(400);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => "El campo $campo es requerido"
                ]);
                return;
            }
        }

        try {
            $resultado = IntentosLogin::registrarIntento(
                $datos['usuario_nombre'],
                (bool)$datos['intento_exitoso'],
                $datos['intento_detalle'] ?? ''
            );

            if ($resultado['resultado'] > 0) {
                echo json_encode([
                    'exito' => true,
                    'mensaje' => 'Intento registrado correctamente',
                    'id' => $resultado['id']
                ]);
            } else {
                http_response_code(500);
                echo json_encode([
                    'exito' => false,
                    'mensaje' => 'Error al registrar el intento'
                ]);
            }
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al registrar',
                'detalle' => $e->getMessage()
            ]);
        }
    }

    public static function verificarBloqueoAPI()
    {
        getHeadersApi();

        if (!isset($_GET['usuario'])) {
            http_response_code(400);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Usuario no proporcionado'
            ]);
            return;
        }

        try {
            $usuario = $_GET['usuario'];
            $minutos = $_GET['minutos'] ?? 30;
            $maxIntentos = $_GET['max_intentos'] ?? 5;

            $intentosFallidos = IntentosLogin::getIntentosFallidos($usuario, $minutos);

            $bloqueado = $intentosFallidos >= $maxIntentos;

            http_response_code(200);
            echo json_encode([
                'exito' => true,
                'bloqueado' => $bloqueado,
                'intentos_fallidos' => $intentosFallidos,
                'max_intentos' => $maxIntentos,
                'minutos' => $minutos,
                'tiempo_restante' => $bloqueado ? $minutos : 0
            ]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode([
                'exito' => false,
                'mensaje' => 'Error al verificar bloqueo',
                'detalle' => $e->getMessage()
            ]);
        }
    }
}
